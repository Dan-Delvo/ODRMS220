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
        $sqlScript .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;

            // Table structure - MariaDB compatible
            $createResult = DB::select("SHOW CREATE TABLE `$tableName`");
            $createTable = $createResult[0]->{'Create Table'};

            $sqlScript .= "\n-- Table: $tableName\n";
            $sqlScript .= "DROP TABLE IF EXISTS `$tableName`;\n";
            $sqlScript .= $createTable . ";\n\n";

            // Table data
            $rows = DB::table($tableName)->get();

            if ($rows->count() > 0) {
                $sqlScript .= "-- Data for table: $tableName\n";

                foreach ($rows as $row) {
                    $row = (array) $row;
                    $columns = array_keys($row);

                    // Escape column names with backticks
                    $columnNames = implode('`, `', $columns);

                    // Prepare values with proper escaping
                    $values = array_map(function($value) {
                        if (is_null($value)) {
                            return 'NULL';
                        }
                        // Use PDO quote for proper escaping in MariaDB
                        return DB::connection()->getPdo()->quote($value);
                    }, $row);

                    $valueString = implode(', ', $values);
                    $sqlScript .= "INSERT INTO `$tableName` (`$columnNames`) VALUES ($valueString);\n";
                }

                $sqlScript .= "\n";
            }
        }

        $sqlScript .= "SET FOREIGN_KEY_CHECKS=1;\n";

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

        $sqlContent = file_get_contents($sqlFilePath);

        try {
            // Set MariaDB-specific settings for restore
            DB::statement("SET FOREIGN_KEY_CHECKS=0;");
            DB::statement("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");
            DB::statement("SET AUTOCOMMIT=0;");
            DB::beginTransaction();

            // Split SQL content by semicolons (respecting multi-line statements)
            $queries = $this->splitSqlQueries($sqlContent);

            foreach ($queries as $query) {
                $query = trim($query);

                // Skip empty queries and comments
                if (empty($query) || str_starts_with($query, '--') || str_starts_with($query, '/*')) {
                    continue;
                }

                DB::statement($query);
            }

            DB::commit();
            DB::statement("SET AUTOCOMMIT=1;");
            DB::statement("SET FOREIGN_KEY_CHECKS=1;");

            unlink($sqlFilePath);

        } catch (\Exception $e) {
            DB::rollBack();
            DB::statement("SET AUTOCOMMIT=1;");
            DB::statement("SET FOREIGN_KEY_CHECKS=1;");

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

    /**
     * Split SQL content into individual queries
     * Handles multi-line statements and string literals properly
     */
    private function splitSqlQueries($sql)
    {
        $queries = [];
        $currentQuery = '';
        $inString = false;
        $stringChar = '';
        $lines = explode("\n", $sql);

        foreach ($lines as $line) {
            $line = rtrim($line);

            // Skip comment lines
            if (preg_match('/^\s*(--|#)/', $line)) {
                continue;
            }

            // Remove inline comments
            $line = preg_replace('/\s+--.*$/', '', $line);

            if (empty(trim($line))) {
                continue;
            }

            for ($i = 0; $i < strlen($line); $i++) {
                $char = $line[$i];

                // Handle string literals
                if (($char === '"' || $char === "'") && ($i === 0 || $line[$i - 1] !== '\\')) {
                    if (!$inString) {
                        $inString = true;
                        $stringChar = $char;
                    } elseif ($char === $stringChar) {
                        $inString = false;
                    }
                }

                $currentQuery .= $char;

                // End of query
                if ($char === ';' && !$inString) {
                    $queries[] = trim($currentQuery);
                    $currentQuery = '';
                }
            }

            $currentQuery .= "\n";
        }

        // Add any remaining query
        if (!empty(trim($currentQuery))) {
            $queries[] = trim($currentQuery);
        }

        return $queries;
    }
}
