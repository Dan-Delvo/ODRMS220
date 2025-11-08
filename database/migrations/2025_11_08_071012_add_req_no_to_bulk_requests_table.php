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
        DB::statement('SET @i = 0');

        // Update req_no with sequential numbering based on request_date
        DB::unprepared("
            UPDATE bulk_requests
            JOIN (
                SELECT Request_ID, (@i := @i + 1) AS new_no
                FROM bulk_requests
                ORDER BY request_date ASC
            ) AS sorted
            ON bulk_requests.Request_ID = sorted.Request_ID
            SET bulk_requests.req_no = CONCAT('2025-', LPAD(sorted.new_no, 4, '0'))
        ");
    }

    public function down()
    {
        Schema::table('bulk_requests', function (Blueprint $table) {
            $table->dropColumn('req_no');
        });
    }
};
