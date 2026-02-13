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
        // Add columns to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'salary')) {
                $table->string('salary', 50)->nullable()->after('position');
            }
            if (!Schema::hasColumn('users', 'recommending_approver')) {
                $table->string('recommending_approver', 100)->nullable()->after('salary');
            }
            if (!Schema::hasColumn('users', 'final_approver')) {
                $table->string('final_approver', 100)->nullable()->after('recommending_approver');
            }
        });

        // Create signatories table
        Schema::create('signatories', function (Blueprint $table) {
            $table->id();
            $table->string('position', 100)->unique();
            $table->string('name', 200)->nullable();
            $table->timestamps();
        });

        // Seed default positions
        $positions = [
            'CID CHIEF',
            'SGOD CHIEF',
            'AO',
            'ASDS',
            'SDS'
        ];

        foreach ($positions as $pos) {
            DB::table('signatories')->insertOrIgnore([
                'position' => $pos, 
                'created_at' => now(), 
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['salary', 'recommending_approver', 'final_approver']);
        });

        Schema::dropIfExists('signatories');
    }
};
