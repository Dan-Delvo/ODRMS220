<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add sync columns to std_students table
        if (Schema::connection('mysql_local')->hasTable('std_students')) {
            Schema::connection('mysql_local')->table('std_students', function (Blueprint $table) {
                if (!Schema::connection('mysql_local')->hasColumn('std_students', 'synced')) {
                    $table->boolean('synced')->default(false);
                    $table->boolean('needs_sync')->default(false);
                    $table->timestamp('synced_at')->nullable();
                    $table->unsignedBigInteger('online_id')->nullable();
                }
            });
        }

        // Add sync columns to acc_users table
        if (Schema::connection('mysql_local')->hasTable('acc_users')) {
            Schema::connection('mysql_local')->table('acc_users', function (Blueprint $table) {
                if (!Schema::connection('mysql_local')->hasColumn('acc_users', 'synced')) {
                    $table->boolean('synced')->default(false);
                    $table->boolean('needs_sync')->default(false);
                    $table->timestamp('synced_at')->nullable();
                    $table->unsignedBigInteger('online_id')->nullable();
                }
            });
        }

        // Add sync columns to doc_requests table
        if (Schema::connection('mysql_local')->hasTable('doc_requests')) {
            Schema::connection('mysql_local')->table('doc_requests', function (Blueprint $table) {
                if (!Schema::connection('mysql_local')->hasColumn('doc_requests', 'synced')) {
                    $table->boolean('synced')->default(false);
                    $table->boolean('needs_sync')->default(false);
                    $table->timestamp('synced_at')->nullable();
                    $table->unsignedBigInteger('online_id')->nullable();
                }
            });
        }

        // Add sync columns to clm_claimers table
        if (Schema::connection('mysql_local')->hasTable('clm_claimers')) {
            Schema::connection('mysql_local')->table('clm_claimers', function (Blueprint $table) {
                if (!Schema::connection('mysql_local')->hasColumn('clm_claimers', 'synced')) {
                    $table->boolean('synced')->default(false);
                    $table->boolean('needs_sync')->default(false);
                    $table->timestamp('synced_at')->nullable();
                    $table->unsignedBigInteger('online_id')->nullable();
                }
            });
        }
    }

    public function down()
    {
        Schema::connection('mysql_local')->table('std_students', function (Blueprint $table) {
            $table->dropColumn(['synced', 'needs_sync', 'synced_at', 'online_id']);
        });

        Schema::connection('mysql_local')->table('acc_users', function (Blueprint $table) {
            $table->dropColumn(['synced', 'needs_sync', 'synced_at', 'online_id']);
        });

        Schema::connection('mysql_local')->table('doc_requests', function (Blueprint $table) {
            $table->dropColumn(['synced', 'needs_sync', 'synced_at', 'online_id']);
        });

        Schema::connection('mysql_local')->table('clm_claimers', function (Blueprint $table) {
            $table->dropColumn(['synced', 'needs_sync', 'synced_at', 'online_id']);
        });
    }
};
