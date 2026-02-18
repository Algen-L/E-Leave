<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveType;
use App\Models\LeaveCreditPolicy;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create the Leave Type
        $id = DB::table('leave_types')->insertGetId([
            'type_name' => 'Wellness Leave',
            'description' => 'Special leave for health and wellness. 5 days per year, max 3 consecutive days.',
            'category' => 'Non-Cummulative', 
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Create the Policy
        DB::table('leave_credit_policies')->insert([
            'leave_type_id' => $id,
            'accrual_rate' => 5.00,
            'accrual_period' => 'Yearly', 
            'expiration_rule' => 'Yearly', 
            'max_credits' => 5.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $type = DB::table('leave_types')->where('type_name', 'Wellness Leave')->first();
        
        if ($type) {
             DB::table('leave_credit_policies')->where('leave_type_id', $type->id)->delete();
             DB::table('leave_types')->where('id', $type->id)->delete();
        }
    }
};
