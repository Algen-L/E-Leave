<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OfficerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $officers = [
            [
                'username' => 'cid_chief',
                'name' => 'CID Chief Officer',
                'full_name' => 'Juan Dela Cruz (CID)',
                'role' => 'cid_chief',
                'office_station' => 'CID Office',
                'position' => 'Chief Education Supervisor',
            ],
            [
                'username' => 'sgod_chief',
                'name' => 'SGOD Chief Officer',
                'full_name' => 'Maria Clara (SGOD)',
                'role' => 'sgod_chief',
                'office_station' => 'SGOD Office',
                'position' => 'Chief Education Supervisor',
            ],
            [
                'username' => 'ao_officer',
                'name' => 'Admin Officer',
                'full_name' => 'Jose Rizal (AO)',
                'role' => 'ao',
                'office_station' => 'Office of the SDS',
                'position' => 'Administrative Officer V',
            ],
            [
                'username' => 'asds_officer',
                'name' => 'ASDS Officer',
                'full_name' => 'Andres Bonifacio (ASDS)',
                'role' => 'asds',
                'office_station' => 'Office of the ASDS',
                'position' => 'Asst. Schools Division Superintendent',
            ],
            [
                'username' => 'sds_officer',
                'name' => 'SDS Officer',
                'full_name' => 'Gabriela Silang (SDS)',
                'role' => 'sds',
                'office_station' => 'Office of the SDS',
                'position' => 'Schools Division Superintendent',
            ],
        ];

        foreach ($officers as $officer) {
            User::firstOrCreate(
                ['username' => $officer['username']],
                [
                    'name' => $officer['name'],
                    'gmail' => $officer['username'] . '@deped.gov.ph',
                    'password' => Hash::make('password123'),
                    'full_name' => $officer['full_name'],
                    'role' => $officer['role'],
                    'office_station' => $officer['office_station'],
                    'position' => $officer['position'],
                    'is_active' => true,
                ]
            );
        }
    }
}
