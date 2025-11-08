<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArchiveOldRecords extends Command
{
    protected $signature = 'db:archive-records';
    protected $description = 'Archive old records from specific tables';

    public function handle()
    {
        $archiveBefore = Carbon::now()->subYear();    // older than 1 year
        $deleteBefore  = Carbon::now()->subYears(5); // delete from archives older than 5 years

        DB::transaction(function () use ($archiveBefore) {

            // 1) doc_requests → doc_requests_archive
            DB::statement("
                INSERT INTO doc_requests_archive
                SELECT * FROM doc_requests
                WHERE request_date < ?
            ", [$archiveBefore]);

            DB::statement("
                DELETE FROM doc_requests
                WHERE request_date < ?
            ", [$archiveBefore]);

            // 2) clm_claimers → clm_claimers_archive (linked via doc_requests)
            DB::statement("
                INSERT INTO clm_claimers_archive
                SELECT c.*
                FROM clm_claimers c
                JOIN doc_requests d ON d.clm_claimers_id = c.id
                WHERE d.request_date < ?
            ", [$archiveBefore]);

            DB::statement("
                DELETE c
                FROM clm_claimers c
                JOIN doc_requests_archive d ON d.clm_claimers_id = c.id
            ");

            // 3) bulk_requests → bulk_requests_archive
            DB::statement("
                INSERT INTO bulk_requests_archive
                SELECT * FROM bulk_requests
                WHERE request_date < ?
            ", [$archiveBefore]);

            DB::statement("
                DELETE FROM bulk_requests
                WHERE request_date < ?
            ", [$archiveBefore]);

            // 4) bulk_students → bulk_students_archive (linked via bulk_requests)
            DB::statement("
                INSERT INTO bulk_students_archive
                SELECT s.*
                FROM bulk_students s
                JOIN bulk_requests r ON r.Request_ID = s.Request_ID
                WHERE r.request_date < ?
            ", [$archiveBefore]);

            DB::statement("
                DELETE s
                FROM bulk_students s
                JOIN bulk_requests_archive r ON r.Request_ID = s.Request_ID
            ");
        });

        // Delete very old records from archives (>5 years)
        DB::statement("DELETE FROM doc_requests_archive WHERE request_date < ?", [$deleteBefore]);

        DB::statement("
            DELETE c
            FROM clm_claimers_archive c
            JOIN doc_requests_archive d ON d.clm_claimers_id = c.id
            WHERE d.request_date < ?
        ", [$deleteBefore]);

        DB::statement("DELETE FROM bulk_requests_archive WHERE request_date < ?", [$deleteBefore]);

        DB::statement("
            DELETE s
            FROM bulk_students_archive s
            JOIN bulk_requests_archive r ON r.Request_ID = s.Request_ID
            WHERE r.request_date < ?
        ", [$deleteBefore]);

        $this->info("Archiving completed successfully.");
    }
}
