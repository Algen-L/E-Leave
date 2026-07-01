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
        // 1. Create roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->timestamps();
        });

        // 2. Create role_user pivot table
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->unique(['user_id', 'role_id']);
        });

        // 3. Seed roles
        $roles = [
            ['name' => 'user', 'display_name' => 'User'],
            ['name' => 'hr_review_officer', 'display_name' => 'HR Review Officer'],
            ['name' => 'record_personnel', 'display_name' => 'Record Personnel'],
            ['name' => 'sds', 'display_name' => 'SDS'],
            ['name' => 'asds', 'display_name' => 'ASDS'],
            ['name' => 'sgod_chief', 'display_name' => 'SGOD Chief'],
            ['name' => 'cid_chief', 'display_name' => 'CID Chief'],
            ['name' => 'ao', 'display_name' => 'AO'],
            ['name' => 'super_admin', 'display_name' => 'Super Admin'],
            ['name' => 'admin', 'display_name' => 'Admin'],
            ['name' => 'head_hr', 'display_name' => 'HR Personnel'],
            ['name' => 'hr', 'display_name' => 'HR Staff'],
            ['name' => 'immediate_head', 'display_name' => 'Immediate Head'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert(array_merge($role, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        // 4. Migrate existing user roles
        $users = DB::table('users')->get();
        $roleMap = DB::table('roles')->pluck('id', 'name')->toArray();

        foreach ($users as $user) {
            $userRole = $user->role ?: 'user';
            
            // Normalize role mapping for head_hr / hr personnel / admin
            if ($userRole === 'admin') {
                $userRole = 'admin';
            }

            // Assign standard role 'user'
            if (isset($roleMap['user'])) {
                DB::table('role_user')->insertOrIgnore([
                    'user_id' => $user->id,
                    'role_id' => $roleMap['user'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Assign additional current role if it's not 'user'
            if ($userRole !== 'user' && isset($roleMap[$userRole])) {
                DB::table('role_user')->insertOrIgnore([
                    'user_id' => $user->id,
                    'role_id' => $roleMap[$userRole],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
};
