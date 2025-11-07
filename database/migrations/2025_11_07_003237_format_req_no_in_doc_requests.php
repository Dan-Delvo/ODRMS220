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

        DB::unprepared("
            UPDATE doc_requests d
            JOIN (
                SELECT
                    id,
                    DATE_FORMAT(request_date, '%Y') AS yr,
                    @row := IF(@current_year = DATE_FORMAT(request_date, '%Y'), @row + 1, 1) AS seq,
                    @current_year := DATE_FORMAT(request_date, '%Y') AS dummy
                FROM doc_requests, (SELECT @row := 0, @current_year := '') AS vars
                ORDER BY request_date, id
            ) AS t ON d.id = t.id
            SET d.req_no = CONCAT(t.yr, '-', LPAD(t.seq, 2, '0'));
        ");


    }

    public function down()
    {
        Schema::table('doc_requests', function (Blueprint $table) {
            $table->bigInteger('req_no')->unsigned()->autoIncrement()->change();
        });
    }
};
