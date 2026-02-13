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
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->foreignId('recommending_officer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approving_officer_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Workflow timestamps
            $table->timestamp('hr_verified_at')->nullable();
            $table->timestamp('recommended_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            
            // Remarks/Notes
            $table->text('rejection_remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->dropForeign(['recommending_officer_id']);
            $table->dropForeign(['approving_officer_id']);
            $table->dropColumn([
                'recommending_officer_id', 
                'approving_officer_id', 
                'hr_verified_at', 
                'recommended_at', 
                'approved_at', 
                'rejected_at',
                'rejection_remarks'
            ]);
        });
    }
};
