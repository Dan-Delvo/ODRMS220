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
        $fileName = "backup-" . date('Y-m-d_H-i-s') . ".sql";
        $zipFileName = "backup-" . date('Y-m-d_H-i-s') . ".zip";

        // Check if we've already logged this specific backup in this session
        $sessionKey = 'backup_logged_' . $fileName;

        if (!session()->has($sessionKey)) {
            $user = Auth::user();

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

            // Mark this backup as logged
            session()->put($sessionKey, true);
        }

        $database = env('DB_DATABASE');
        $tables = DB::select('SHOW TABLES');
        $tableKey = "Tables_in_{$database}";
        $sqlScript = "";

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            $createTableRow = DB::select("SHOW CREATE TABLE {$tableName}")[0];
            $createTable = array_values((array) $createTableRow)[1];
            $sqlScript .= "\n\n" . $createTable . ";\n\n";

            $rows = DB::table($tableName)->get();
            foreach ($rows as $row) {
                $values = array_map(function ($value) {
                    return isset($value) ? addslashes($value) : 'NULL';
                }, (array) $row);
                $values = "'" . implode("','", $values) . "'";
                $sqlScript .= "INSERT INTO {$tableName} VALUES ({$values});\n";
            }
            $sqlScript .= "\n";
        }

        // Create a temporary file for the SQL
        $tempSqlPath = storage_path('app/temp/' . $fileName);
        $tempZipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        // Save SQL to temporary file
        file_put_contents($tempSqlPath, $sqlScript);

        // Create password-protected ZIP
        $zip = new \ZipArchive();
        if ($zip->open($tempZipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $zip->addFile($tempSqlPath, $fileName);
            $zip->setPassword('ubnhsregistrarpass');
            $zip->setEncryptionName($fileName, \ZipArchive::EM_AES_256);
            $zip->close();
        }

        // Delete temporary SQL file
        unlink($tempSqlPath);

        // Return the ZIP file and delete after sending
        return response()->download($tempZipPath, $zipFileName)->deleteFileAfterSend(true);
    }


    public function restoreBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,txt'
        ]);

        $sql = file_get_contents($request->file('backup_file')->getRealPath());

        // Split queries by semicolon
        $queries = array_filter(array_map('trim', explode(";", $sql)));

        foreach ($queries as $query) {
            try {
                DB::statement($query);
            } catch (\Exception $e) {
                // Ignore errors like duplicate keys etc.
            }
        }

        $fileName = $request->file('backup_file')->getClientOriginalName();
        $user = Auth::user();
        AuditTable::withoutEvents(function () use ($fileName, $user) {
            AuditTable::create([
                'type'          => 'Restore',
                'old_data'      => null,
                'new_data'      => json_encode([
                    'File Name' => $fileName,
                ]),
                'time'          => now(),
                'changedBy'     => $user->studentInformation->full_name,
                'fromTableName' => 'Database Restore'
            ]);
        });

        return back()->with('success', 'Database restored successfully!');
    }

}
