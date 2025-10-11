<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doc_requests', function (Blueprint $table) {
            // Add columns to track offline requests
            $table->boolean('was_offline')->default(false)->after('request_mode');
            $table->timestamp('synced_at')->nullable()->after('was_offline');
            $table->string('offline_id')->nullable()->after('synced_at');
            $table->integer('sync_attempts')->default(0)->after('offline_id');

            // Add index for offline tracking
            $table->index(['was_offline', 'synced_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doc_requests', function (Blueprint $table) {
            $table->dropIndex(['was_offline', 'synced_at']);
            $table->dropColumn(['was_offline', 'synced_at', 'offline_id', 'sync_attempts']);
        });
    }
};
