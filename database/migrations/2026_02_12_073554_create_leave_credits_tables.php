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
        // 1. Leave Credits Table (Per User, Per Type)
        Schema::create('leave_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained()->cascadeOnDelete();
            $table->decimal('credits', 8, 2)->default(0); 
            $table->boolean('is_locked')->default(false); // Locked after initial input by HR
            $table->timestamps();
            
            // Allow multiple types per user, but one entry per type
            $table->unique(['user_id', 'leave_type_id']);
        });

        // 2. Leave Credit Policies (Set by Head HR)
        Schema::create('leave_credit_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_type_id')->constrained()->cascadeOnDelete();
            
            // Accrual Settings
            $table->decimal('accrual_rate', 8, 2)->default(0); // e.g. 1.25
            $table->string('accrual_period')->default('Monthly'); // Monthly, Yearly, None
            
            // Expiration Settings
            $table->string('expiration_rule')->default('None'); // None, Yearly, Monthly, SpecificDate
            $table->date('expiration_date')->nullable(); // If SpecificDate
            
            // Cap Settings
            $table->decimal('max_credits', 8, 2)->nullable(); // Null = No limit
            
            $table->timestamps();
            $table->unique('leave_type_id');
        });

        // 3. Leave Credit Audit Logs (For Head HR to view HR actions)
        Schema::create('leave_credit_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('actor_id')->nullable(); // Who did it (HR)
            $table->unsignedBigInteger('target_user_id')->nullable(); // Who got credits
            $table->string('action'); // 'allocate', 'update', 'deduct'
            $table->string('leave_type_name');
            $table->decimal('previous_value', 8, 2)->nullable();
            $table->decimal('new_value', 8, 2)->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
            
            $table->foreign('actor_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('target_user_id')->references('id')->on('users')->nullOnDelete();
        });
        
        // 4. Leave Credit Update Requests (HR asking permission to edit again)
        Schema::create('leave_update_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requester_id')->nullable(); // HR Staff
            $table->unsignedBigInteger('approver_id')->nullable(); // Head HR
            $table->unsignedBigInteger('target_user_id')->nullable(); 
            $table->unsignedBigInteger('leave_type_id')->nullable();
            
            $table->text('reason');
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected
            $table->timestamps();
            
            $table->foreign('requester_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('approver_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('target_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_update_requests');
        Schema::dropIfExists('leave_credit_audit_logs');
        Schema::dropIfExists('leave_credit_policies');
        Schema::dropIfExists('leave_credits');
    }
};
