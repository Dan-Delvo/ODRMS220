<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bulk_students', function (Blueprint $table) {
            $table->id('Student_ID');
            $table->unsignedBigInteger('Request_ID');
            $table->string('Student_Name');

            $table->foreign('Request_ID')
                  ->references('Request_ID')
                  ->on('bulk_requests')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulk_students');
    }
};
