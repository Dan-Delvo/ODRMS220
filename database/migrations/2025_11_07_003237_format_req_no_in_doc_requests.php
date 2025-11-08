<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('doc_requests', function (Blueprint $table) {
            $table->string('req_no', 20)->change();
        });

        // Initialize counter variable
        DB::statement('SET @i = 0');

        // Update req_no with sequential numbering based on request_date and request_time
        DB::unprepared("
            UPDATE doc_requests
            JOIN (
                SELECT id, (@i := @i + 1) AS new_no
                FROM doc_requests
                ORDER BY request_date ASC, request_time ASC
            ) AS sorted
            ON doc_requests.id = sorted.id
            SET doc_requests.req_no = CONCAT('2025-', LPAD(sorted.new_no, 4, '0'))
        ");
    }

    public function down()
    {
        Schema::table('doc_requests', function (Blueprint $table) {
            $table->bigInteger('req_no')->unsigned()->autoIncrement()->change();
        });
    }
};
