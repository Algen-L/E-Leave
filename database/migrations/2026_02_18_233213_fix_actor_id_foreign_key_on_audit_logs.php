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
        // 1. Leave Credit Audit Logs (Modifying actor_id)
        Schema::table('leave_credit_audit_logs', function (Blueprint $table) {
            // Drop existing FK
            $table->dropForeign(['actor_id']);
            $table->dropForeign(['target_user_id']);
            
            // Make columns nullable
            $table->unsignedBigInteger('actor_id')->nullable()->change();
            $table->unsignedBigInteger('target_user_id')->nullable()->change();

            // Add new FK with SET NULL
            $table->foreign('actor_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('target_user_id')->references('id')->on('users')->nullOnDelete();
        });

        // 2. Leave Update Requests (Modifying requester_id, approver_id, target_user_id)
        if (Schema::hasTable('leave_update_requests')) {
            Schema::table('leave_update_requests', function (Blueprint $table) {
                $table->dropForeign(['requester_id']);
                $table->dropForeign(['approver_id']);
                $table->dropForeign(['target_user_id']);
                
                $table->unsignedBigInteger('requester_id')->nullable()->change();
                $table->unsignedBigInteger('approver_id')->nullable()->change();
                // target_user_id remains not nullable or we cascade delete the request?
                // Let's cascade delete the REQUEST if the target user is deleted
                
                $table->foreign('requester_id')->references('id')->on('users')->nullOnDelete();
                $table->foreign('approver_id')->references('id')->on('users')->nullOnDelete();
                $table->foreign('target_user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is complex to reverse perfectly due to nullable changes, but we try restoration
        Schema::table('leave_credit_audit_logs', function (Blueprint $table) {
            $table->dropForeign(['actor_id']);
            $table->dropForeign(['target_user_id']);
            
            // This might fail if there are NULLs now, but for rollback logic:
            // $table->unsignedBigInteger('actor_id')->nullable(false)->change(); 
            
            $table->foreign('actor_id')->references('id')->on('users'); 
            $table->foreign('target_user_id')->references('id')->on('users');
        });
    }
};
