<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Office;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = [
            // OSDS Offices
            ['category' => 'OSDS', 'name' => 'ADMINISTRATIVE'],
            ['category' => 'OSDS', 'name' => 'ADMINISTRATIVE (PERSONEL)'],
            ['category' => 'OSDS', 'name' => 'ADMINISTRATIVE (PROPERTY AND SUPPLY)'],
            ['category' => 'OSDS', 'name' => 'ADMINISTRATIVE (RECORDS)'],
            ['category' => 'OSDS', 'name' => 'ADMINISTRATIVE (CASH)'],
            ['category' => 'OSDS', 'name' => 'ADMINISTRATIVE (PROCUREMENT)'],
            ['category' => 'OSDS', 'name' => 'ADMINISTRATIVE (GENERAL SERVICES)'],
            ['category' => 'OSDS', 'name' => 'FINANCE (ACCOUNTING)'],
            ['category' => 'OSDS', 'name' => 'FINANCE (BUDGET)'],
            ['category' => 'OSDS', 'name' => 'LEGAL'],
            ['category' => 'OSDS', 'name' => 'ICT'],
            
            // SGOD Offices
            ['category' => 'SGOD', 'name' => 'SGOD (SCHOOL MANAGEMENT MONITORING & EVALUATION)'],
            ['category' => 'SGOD', 'name' => 'SGOD (HUMAN RESOURCES DEVELOPMENT)'],
            ['category' => 'SGOD', 'name' => 'SGOD (SOCIAL MOBILIZATION AND NETWORKING)'],
            ['category' => 'SGOD', 'name' => 'SGOD (PLANNING AND RESEARCH)'],
            ['category' => 'SGOD', 'name' => 'SGOD (DISASTER RISK REDUCTION AND MANAGEMENT)'],
            ['category' => 'SGOD', 'name' => 'SGOD (EDUCATION FACILITIES)'],
            ['category' => 'SGOD', 'name' => 'SGOD (SCHOOL HEALTH AND NUTRITION)'],
            ['category' => 'SGOD', 'name' => 'SGOD (SCHOOL HEALTH AND NUTRITION) (DENTAL)'],
            ['category' => 'SGOD', 'name' => 'SGOD (SCHOOL HEALTH AND NUTRITION) (MEDICAL)'],
            
            // CID Offices
            ['category' => 'CID', 'name' => 'CID (INSTRUCTIONAL MANAGEMENT)'],
            ['category' => 'CID', 'name' => 'CID (LEARNING RESOURCES MANAGEMENT)'],
            ['category' => 'CID', 'name' => 'CID (ALTERNATIVE LEARNING SYSTEM)'],
            ['category' => 'CID', 'name' => 'CID (DISTRICT INSTRUCTIONAL SUPERVISION)'],
        ];

        foreach ($offices as $office) {
            Office::firstOrCreate(
                ['name' => $office['name']],
                ['category' => $office['category']]
            );
        }
    }
}
