<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        User::firstOrCreate(
            ['username' => 'super_admin'],
            [
                'name' => 'Super Administrator',
                'gmail' => 'superadmin@sdo-temp.local',
                'password' => Hash::make('password123'),
                'full_name' => 'Super Administrator',
                'office_station' => 'ICT',
                'position' => 'System Administrator',
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        // Create Regular Admin
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'gmail' => 'admin@sdo-temp.local',
                'password' => Hash::make('password123'),
                'full_name' => 'Administrator',
                'office_station' => 'ADMINISTRATIVE',
                'position' => 'Admin Officer',
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Create Head HR
        User::firstOrCreate(
            ['username' => 'headhr'],
            [
                'name' => 'Head HR Officer',
                'gmail' => 'headhr@sdo-temp.local',
                'password' => Hash::make('password123'),
                'full_name' => 'Head HR Officer',
                'office_station' => 'SGOD (HUMAN RESOURCES DEVELOPMENT)',
                'position' => 'HR Head',
                'role' => 'head_hr',
                'is_active' => true,
            ]
        );

        // Create HR
        User::firstOrCreate(
            ['username' => 'hr'],
            [
                'name' => 'HR Officer',
                'gmail' => 'hr@sdo-temp.local',
                'password' => Hash::make('password123'),
                'full_name' => 'HR Officer',
                'office_station' => 'SGOD (HUMAN RESOURCES DEVELOPMENT)',
                'position' => 'HR Staff',
                'role' => 'hr',
                'is_active' => true,
            ]
        );

        // Create Regular User
        User::firstOrCreate(
            ['username' => 'testuser'],
            [
                'name' => 'Test User',
                'gmail' => 'testuser@sdo-temp.local',
                'password' => Hash::make('password123'),
                'full_name' => 'Test User',
                'office_station' => 'ICT',
                'position' => 'Staff',
                'role' => 'user',
                'is_active' => true,
            ]
        );
    }
}
