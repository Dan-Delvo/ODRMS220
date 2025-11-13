<?php
// Create new migration: php artisan make:migration add_reason_column_to_doc_requests_table

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
        Schema::table('doc_requests', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('release_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc_requests', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
};
