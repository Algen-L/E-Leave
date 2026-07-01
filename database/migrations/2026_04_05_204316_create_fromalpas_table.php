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
        Schema::create('fromalpas', function (Blueprint $table) {
            $table->id();
            $table->string('employee_no', 50);
            $table->string('full_name', 150);
            $table->integer('leave_credits');
            $table->string('source_system', 50);
            $table->string('source_reference', 100)->nullable();
            
            // System columns for tracking
            $table->decimal('processed_credits', 8, 2)->default(0); 
            
            $table->timestamps();

            // Unique key as requested
            $table->unique(['employee_no', 'source_reference'], 'unique_deduction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fromalpas');
    }
};
