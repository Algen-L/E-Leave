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
        Schema::table('users', function (Blueprint $table) {
            // Only add columns that don't already exist
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username', 50)->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'full_name')) {
                $table->string('full_name', 100)->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'office_station')) {
                $table->string('office_station', 100)->nullable()->after('full_name');
            }
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position', 100)->nullable()->after('office_station');
            }
            if (!Schema::hasColumn('users', 'employee_number')) {
                $table->string('employee_number', 100)->nullable()->after('position');
            }
            if (!Schema::hasColumn('users', 'rating_period')) {
                $table->string('rating_period', 100)->nullable()->after('employee_number');
            }
            if (!Schema::hasColumn('users', 'area_of_specialization')) {
                $table->string('area_of_specialization', 100)->nullable()->after('rating_period');
            }
            if (!Schema::hasColumn('users', 'age')) {
                $table->integer('age')->nullable()->after('area_of_specialization');
            }
            if (!Schema::hasColumn('users', 'sex')) {
                $table->string('sex', 20)->nullable()->after('age');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 20)->default('user')->after('sex');
            }
            if (!Schema::hasColumn('users', 'profile_picture')) {
                $table->string('profile_picture', 255)->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(false)->after('profile_picture');
            }
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('users', 'passkey')) {
                $table->string('passkey', 6)->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('users', 'passkey_expires_at')) {
                $table->datetime('passkey_expires_at')->nullable()->after('passkey');
            }
        });

        // Rename email to gmail if email column exists and gmail doesn't
        if (Schema::hasColumn('users', 'email') && !Schema::hasColumn('users', 'gmail')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('email', 'gmail');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'gmail') && !Schema::hasColumn('users', 'email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('gmail', 'email');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $columns = ['username', 'full_name', 'office_station', 'position', 'employee_number',
                        'rating_period', 'area_of_specialization', 'age', 'sex', 'role',
                        'profile_picture', 'is_active', 'created_by', 'passkey', 'passkey_expires_at'];
            
            $toDrop = array_filter($columns, fn($col) => Schema::hasColumn('users', $col));
            
            if (!empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });
    }
};
