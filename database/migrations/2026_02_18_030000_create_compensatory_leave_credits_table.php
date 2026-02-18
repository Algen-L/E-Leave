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
        Schema::create('compensatory_leave_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('cascade');
            $table->decimal('credits', 8, 2);
            $table->decimal('remaining_credits', 8, 2);
            $table->date('expiration_date'); // The specific expiration date for this credit entry
            $table->string('remarks')->nullable();
            $table->enum('status', ['Active', 'Consumed', 'Expired'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compensatory_leave_credits');
    }
};
