<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE leave_credits MODIFY COLUMN credits DECIMAL(10, 3) NOT NULL DEFAULT 0.000');
        DB::statement('ALTER TABLE leave_credit_policies MODIFY COLUMN accrual_rate DECIMAL(10, 3) NOT NULL DEFAULT 0.000');
        DB::statement('ALTER TABLE leave_credit_policies MODIFY COLUMN max_credits DECIMAL(10, 3) NULL');
        DB::statement('ALTER TABLE leave_credit_audit_logs MODIFY COLUMN previous_value DECIMAL(10, 3) NULL');
        DB::statement('ALTER TABLE leave_credit_audit_logs MODIFY COLUMN new_value DECIMAL(10, 3) NULL');
        DB::statement('ALTER TABLE compensatory_leave_credits MODIFY COLUMN credits DECIMAL(10, 3) NOT NULL');
        DB::statement('ALTER TABLE compensatory_leave_credits MODIFY COLUMN remaining_credits DECIMAL(10, 3) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE leave_credits MODIFY COLUMN credits DECIMAL(8, 2) NOT NULL DEFAULT 0.00');
        DB::statement('ALTER TABLE leave_credit_policies MODIFY COLUMN accrual_rate DECIMAL(8, 2) NOT NULL DEFAULT 0.00');
        DB::statement('ALTER TABLE leave_credit_policies MODIFY COLUMN max_credits DECIMAL(8, 2) NULL');
        DB::statement('ALTER TABLE leave_credit_audit_logs MODIFY COLUMN previous_value DECIMAL(8, 2) NULL');
        DB::statement('ALTER TABLE leave_credit_audit_logs MODIFY COLUMN new_value DECIMAL(8, 2) NULL');
        DB::statement('ALTER TABLE compensatory_leave_credits MODIFY COLUMN credits DECIMAL(8, 2) NOT NULL');
        DB::statement('ALTER TABLE compensatory_leave_credits MODIFY COLUMN remaining_credits DECIMAL(8, 2) NOT NULL');
    }
};
