<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update req_no with sequential numbering per year (year taken from request_date)
        // Format: BR-YYYY-#### (e.g., BR-2025-0001)
        // Sequence resets for each year and uses 4-digit padding
        DB::unprepared("
            UPDATE bulk_requests d
            JOIN (
                SELECT
                    Request_ID,
                    DATE_FORMAT(request_date, '%Y') AS yr,
                    @row := IF(@current_year = DATE_FORMAT(request_date, '%Y'), @row + 1, 1) AS seq,
                    @current_year := DATE_FORMAT(request_date, '%Y') AS dummy
                FROM bulk_requests, (SELECT @row := 0, @current_year := '') AS vars
                ORDER BY request_date ASC, Request_ID ASC
            ) AS t ON d.Request_ID = t.Request_ID
            SET d.req_no = CONCAT('BR-', t.yr, '-', LPAD(t.seq, 4, '0'));
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally revert to plain year-number format (YYYY-####)
        DB::unprepared("
            UPDATE bulk_requests
            SET req_no = SUBSTRING(req_no, 4)
            WHERE req_no LIKE 'BR-%';
        ");
    }
};
