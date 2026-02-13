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
        Schema::table('signatories', function (Blueprint $table) {
            if (!Schema::hasColumn('signatories', 'title')) {
                $table->string('title', 200)->nullable()->after('position');
            }
        });

        // Insert Verifier of Leave Credits
        DB::table('signatories')->insertOrIgnore([
            'position' => 'Verifier of Leave Credits',
            'title' => 'ADMINISTRATIVE OFFICER V', // Default, can be changed
            'name' => '',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Update titles for existing positions
        $map = [
            'SGOD CHIEF' => 'CHIEF OF SCHOOL GOVERNANCE OPERATION DIVISION',
            'CID CHIEF' => 'CHIEF OF CURRICULUM IMPLEMENTATION DIVISION',
            'AO' => 'ADMINISTRATIVE OFFICER V',
            'ASDS' => 'ASST. SCHOOLS DIVISION SUPERINTENDENT OFFICER-IN-CHARGE',
            'SDS' => 'SCHOOLS DIVISION SUPERINTENDENT',
        ];

        foreach ($map as $key => $title) {
            DB::table('signatories')->where('position', $key)->update(['title' => $title]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signatories', function (Blueprint $table) {
            $table->dropColumn('title');
        });
        
        DB::table('signatories')->where('position', 'Verifier of Leave Credits')->delete();
    }
};
