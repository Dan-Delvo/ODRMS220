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
        $database = env('DB_DATABASE');
        $tables = DB::select('SHOW TABLES');
        $tableKey = "Tables_in_{$database}";
        $sqlScript = "";

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;

            // Get table creation query safely
            $createTableRow = DB::select("SHOW CREATE TABLE {$tableName}")[0];
            $createTable = array_values((array) $createTableRow)[1];

            $sqlScript .= "\n\n" . $createTable . ";\n\n";

            // Get table data
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

        $fileName = "backup-" . date('Y-m-d_H-i-s') . ".sql";
        $user = Auth::user();

        AuditTable::withoutEvents(function () use ($fileName, $user) {
            AuditTable::create([
                'type'          => 'Back Up',
                'old_data'      => null,
                'new_data'      => json_encode([
                    'File Name' => $fileName,
                ]),
                'time'          => now(),
                'changedBy'     => $user->studentInformation->full_name,
                'fromTableName' => 'Database Backup'
            ]);
        });

        return response($sqlScript)
            ->header('Content-Type', 'application/sql')
            ->header('Content-Disposition', "attachment; filename={$fileName}");
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
