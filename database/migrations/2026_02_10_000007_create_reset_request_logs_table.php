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
        Schema::create('reset_request_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255);
            $table->enum('type', ['request', 'resend'])->default('request');
            $table->timestamp('requested_at')->useCurrent();
            
            $table->index(['email', 'requested_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reset_request_logs');
    }
};
