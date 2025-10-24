<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AuditTable;
use Illuminate\Support\Facades\Auth;

class BackupController extends Controller
{
    public function downloadBackup()
    {
        $fileName = "backup-" . now()->format('Y-m-d_H-i-s') . ".sql";
        $zipFileName = $fileName . ".zip";
        $zipPassword = config('app.backup_password');

        $user = Auth::user();
        // Session key: ensures only one log per user per backup
        $sessionKey = 'backup_logged_' . Auth::id() . '_' . now()->format('Y-m-d_H-i');

        if (!session()->has($sessionKey)) {
            // Prevent double insert in audit table
            if (!AuditTable::where('type', 'Back Up')
                    ->where('changedBy', $user->studentInformation->full_name)
                    ->where('new_data', json_encode(['File Name' => $fileName]))
                    ->exists())
            {
                AuditTable::withoutEvents(function () use ($fileName, $user) {
                    AuditTable::create([
                        'type'          => 'Back Up',
                        'old_data'      => null,
                        'new_data'      => json_encode(['File Name' => $fileName]),
                        'time'          => now(),
                        'changedBy'     => $user->studentInformation->full_name,
                        'fromTableName' => 'Database Backup',
                        'description'   => null
                    ]);
                });
            }

            session()->put($sessionKey, true);
        }

        // Get tables
        $tables = DB::select('SHOW TABLES');
        $database = DB::getDatabaseName();
        $tableKey = "Tables_in_{$database}";
        $sqlScript = "SET FOREIGN_KEY_CHECKS=0;\n";

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;

            // Table structure
            $create = DB::select("SHOW CREATE TABLE `$tableName`")[0]->{'Create Table'};
            $sqlScript .= "\nDROP TABLE IF EXISTS `$tableName`;\n$create;\n";

            // Table data
            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $row = (array) $row;
                $values = implode(',', array_map(fn($v) =>
                    is_null($v) ? 'NULL' : DB::getPdo()->quote($v), $row
                ));
                $sqlScript .= "INSERT INTO `$tableName` VALUES ($values);\n";
            }
        }

        $sqlScript .= "\nSET FOREIGN_KEY_CHECKS=1;\n";

        // Save SQL to temp folder
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) mkdir($tempDir, 0755, true);

        $sqlPath = "$tempDir/$fileName";
        $zipPath = "$tempDir/$zipFileName";

        file_put_contents($sqlPath, $sqlScript);

        // Create encrypted ZIP
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            $zip->addFile($sqlPath, $fileName);
            $zip->setPassword($zipPassword);
            $zip->setEncryptionName($fileName, \ZipArchive::EM_AES_256);
            $zip->close();
        }

        // Delete SQL temp file
        unlink($sqlPath);

        // Return ZIP to user and delete after send
        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }


    public function restoreBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip'
        ]);

        $zipPassword = config('app.backup_password');
        $zip = new \ZipArchive;
        $zipPath = $request->file('backup_file')->getRealPath();

        if ($zip->open($zipPath) !== true) {
            return back()->with('error', 'Could not open backup archive.');
        }

        if ($zip->numFiles === 0) {
            $zip->close();
            return back()->with('error', 'The ZIP file is empty.');
        }

        // Ensure password is set
        if (!$zip->setPassword($zipPassword)) {
            $zip->close();
            return back()->with('error', 'The ZIP file must be password protected.');
        }

        $extractPath = storage_path("app/temp");
        if (!is_dir($extractPath)) mkdir($extractPath, 0755, true);

        // Extract all files
        if (!$zip->extractTo($extractPath)) {
            $zip->close();
            return back()->with('error', 'Incorrect password or corrupted backup file.');
        }

        $zip->close();

        // Find first .sql file in extracted folder (handles subfolders)
        $sqlFiles = glob($extractPath . '/*.sql');
        if (empty($sqlFiles)) {
            // If no .sql file in root, check subfolders
            $subFolders = glob($extractPath . '/*', GLOB_ONLYDIR);
            foreach ($subFolders as $folder) {
                $subSql = glob($folder . '/*.sql');
                if (!empty($subSql)) {
                    $sqlFiles = $subSql;
                    break;
                }
            }
        }

        if (empty($sqlFiles)) {
            return back()->with('error', 'No SQL file found in the backup.');
        }

        $sqlFilePath = $sqlFiles[0];

        if (!file_exists($sqlFilePath)) {
            return back()->with('error', 'Failed to locate SQL file in the backup.');
        }

        $sqlLines = file($sqlFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        try {
            DB::statement("SET FOREIGN_KEY_CHECKS=0;");

            $query = '';
            foreach ($sqlLines as $line) {
                if (str_starts_with(trim($line), '--')) continue;
                $query .= $line;
                if (str_ends_with(trim($line), ';')) {
                    DB::statement($query);
                    $query = '';
                }
            }

            DB::statement("SET FOREIGN_KEY_CHECKS=1;");
            unlink($sqlFilePath);

        } catch (\Exception $e) {
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }

        // Log restore in audit table
        $user = Auth::user();
        AuditTable::withoutEvents(function () use ($sqlFilePath, $user) {
            AuditTable::create([
                'type' => 'Restore',
                'old_data' => null,
                'new_data' => json_encode(['File Name' => basename($sqlFilePath)]),
                'time' => now(),
                'changedBy' => $user->studentInformation->full_name,
                'fromTableName' => 'Database Restore'
            ]);
        });

        return back()->with('success', 'Database restored successfully.');
    }



}
