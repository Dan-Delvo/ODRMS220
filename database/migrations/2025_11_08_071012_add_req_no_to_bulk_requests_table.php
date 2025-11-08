<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bulk_requests', function (Blueprint $table) {
            $table->string('req_no', 20)->nullable()->after('claimed_date');
        });

        // Initialize counter variable
            DB::statement('SET @row = 0, @current_year = ""');

        $currentYear = date('Y');

        // Update req_no with sequential numbering based on request_date
            // Update req_no with sequential numbering per year (year taken from request_date)
            // Sequence resets for each year and uses 4-digit padding: YYYY-0001
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

    public function down()
    {
        Schema::table('bulk_requests', function (Blueprint $table) {
            $table->dropColumn('req_no');
        });
    }
};
