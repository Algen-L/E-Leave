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
        Schema::create('fromdtr', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number', 50); // Matches User.employee_number string type? Let's check.
            $table->integer('total_minutes');
            $table->date('date');
            
            // Tracking columns
            $table->boolean('is_processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            
            $table->timestamps();
            
            $table->index('employee_number');
            $table->index('is_processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fromdtr');
    }
};
