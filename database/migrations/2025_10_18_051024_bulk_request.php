<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bulk_requests', function (Blueprint $table) {
            $table->id('Request_ID');
            $table->string('School_Name');
            $table->string('School_Email');
            $table->string('Doc_Type');
            $table->enum('Status', ['Pending', 'Processing', 'For Release', 'Claimed'])->default('Pending');
            $table->timestamp('request_date')->useCurrent();
            $table->timestamp('approve_date')->nullable();
            $table->timestamp('forRelease_date')->nullable();
            $table->timestamp('claimed_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulk_requests');
    }
};
