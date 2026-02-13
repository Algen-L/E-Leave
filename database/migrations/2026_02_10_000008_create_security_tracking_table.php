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
        Schema::create('security_tracking', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->unique()->index();
            $table->integer('page_visits')->default(0);
            $table->integer('otp_requests')->default(0);
            $table->integer('otp_inputs')->default(0);
            $table->integer('resends')->default(0);
            $table->string('status', 20)->default('Active');
            $table->boolean('is_blocked')->default(false);
            $table->timestamp('last_activity')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_tracking');
    }
};
