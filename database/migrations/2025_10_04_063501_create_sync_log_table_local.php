<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::connection('mysql_local')->hasTable('sync_log')) {
            Schema::connection('mysql_local')->create('sync_log', function (Blueprint $table) {
                $table->id();
                $table->timestamp('synced_at');
                $table->string('direction');
                $table->string('status');
                $table->text('details')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::connection('mysql_local')->dropIfExists('sync_log');
    }
};
