<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bulk_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('claimer_id')->nullable()->after('Status');
            $table->foreign('claimer_id')->references('id')->on('clm_claimers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bulk_requests', function (Blueprint $table) {
            $table->dropForeign(['claimer_id']);
            $table->dropColumn('claimer_id');
        });
    }
};
