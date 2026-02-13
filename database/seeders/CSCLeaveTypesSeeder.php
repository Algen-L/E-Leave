<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveType;
use App\Models\LeaveCreditPolicy;

class CSCLeaveTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define Standard CSC Leave Types
        $types = [
            [
                'name' => 'Vacation Leave',
                'description' => 'Leave for personal reasons, travel, or rest. Accrues 1.25/month. Requires 5 days advance filing.',
                'accrual_rate' => 1.25,
                'accrual_period' => 'Monthly',
                'expiration_rule' => 'None', // Accumulates
                'max_credits' => null, // Unlimited accumulation
            ],
            [
                'name' => 'Sick Leave',
                'description' => 'Leave for illness or medical check-ups. Accrues 1.25/month.',
                'accrual_rate' => 1.25,
                'accrual_period' => 'Monthly',
                'expiration_rule' => 'None',
                'max_credits' => null,
            ],
            [
                'name' => 'Maternity Leave',
                'description' => '105 days with full pay for female employees.',
                'accrual_rate' => 0,
                'accrual_period' => 'None', // Special
                'expiration_rule' => 'None',
                'max_credits' => null,
            ],
            [
                'name' => 'Paternity Leave',
                'description' => '7 days for married male employees.',
                'accrual_rate' => 0, 
                'accrual_period' => 'None', // Per instance, not accrued
                'expiration_rule' => 'None',
                'max_credits' => 7,
            ],
            [
                'name' => 'Special Privilege Leave',
                'description' => '3 days non-cumulative leave for personal milestones.',
                'accrual_rate' => 3.00,
                'accrual_period' => 'Yearly', // Reset annually
                'expiration_rule' => 'Yearly', // Expires end of year
                'max_credits' => 3,
            ],
            [
                'name' => 'Solo Parent Leave',
                'description' => '7 days for solo parents (renewable annually).',
                'accrual_rate' => 7.00,
                'accrual_period' => 'Yearly',
                'expiration_rule' => 'Yearly',
                'max_credits' => 7,
            ],
            [
                'name' => 'Study Leave',
                'description' => 'Up to 6 months to review for Bar/Board exams or complete degrees.',
                'accrual_rate' => 0,
                'accrual_period' => 'None',
                'expiration_rule' => 'None',
                'max_credits' => null,
            ],
            [
                'name' => 'VAWC Leave (RA 9262)',
                'description' => '10 days for victims of violence against women and children.',
                'accrual_rate' => 10.00,
                'accrual_period' => 'Yearly', // Usually renewable
                'expiration_rule' => 'Yearly',
                'max_credits' => 10,
            ],
            [
                'name' => 'Rehabilitation Leave',
                'description' => 'Up to 6 months for work-related injuries.',
                'accrual_rate' => 0,
                'accrual_period' => 'None',
                'expiration_rule' => 'None',
                'max_credits' => null,
            ],
            [
                'name' => 'Special Leave Benefits for Women',
                'description' => 'Up to 2 months for gynecological surgeries (RA 9710).',
                'accrual_rate' => 0,
                'accrual_period' => 'None',
                'expiration_rule' => 'None',
                'max_credits' => 60, // Approx 2 months
            ],
            [
                'name' => 'Special Emergency (Calamity) Leave',
                'description' => '5 days max for employees affected by natural disasters.',
                'accrual_rate' => 5.00,
                'accrual_period' => 'Yearly', // Usually based on declaration, but capped yearly
                'expiration_rule' => 'Yearly',
                'max_credits' => 5,
            ],
            [
                'name' => 'Adoption Leave',
                'description' => 'Leave for adoptive parents.',
                'accrual_rate' => 0,
                'accrual_period' => 'None',
                'expiration_rule' => 'None',
                'max_credits' => null,
            ],
        ];

        foreach ($types as $typeData) {
            // Create or Update Leave Type
            $type = LeaveType::updateOrCreate(
                ['type_name' => $typeData['name']],
                ['description' => $typeData['description']]
            );

            // Create or Update Policy
            LeaveCreditPolicy::updateOrCreate(
                ['leave_type_id' => $type->id],
                [
                    'accrual_rate' => $typeData['accrual_rate'],
                    'accrual_period' => $typeData['accrual_period'],
                    'expiration_rule' => $typeData['expiration_rule'],
                    'max_credits' => $typeData['max_credits'],
                    'expiration_date' => null, // Default
                ]
            );
        }
    }
}
