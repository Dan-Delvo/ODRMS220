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
        // Create doc_requests_archive table with same structure as doc_requests
        DB::statement('CREATE TABLE doc_requests_archive LIKE doc_requests');

        // Create clm_claimers_archive table with same structure as clm_claimers
        DB::statement('CREATE TABLE clm_claimers_archive LIKE clm_claimers');

        // Create bulk_requests_archive table with same structure as bulk_requests
        DB::statement('CREATE TABLE bulk_requests_archive LIKE bulk_requests');

        // Create bulk_students_archive table with same structure as bulk_students
        DB::statement('CREATE TABLE bulk_students_archive LIKE bulk_students');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_requests_archive');
        Schema::dropIfExists('clm_claimers_archive');
        Schema::dropIfExists('bulk_requests_archive');
        Schema::dropIfExists('bulk_students_archive');
    }
};
