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
        Schema::dropIfExists('leave_details_form6');
        Schema::dropIfExists('leave_applications');
        Schema::dropIfExists('leave_types');

        // 1. Leave Types Table
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('type_name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default leave types
        DB::table('leave_types')->insert([
            ['type_name' => 'Vacation Leave', 'description' => 'Leave of absence for personal reasons.', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => 'Sick Leave', 'description' => 'Leave of absence due to illness or medical needs.', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => 'Maternity Leave', 'description' => 'Leave benefit for expectant mothers.', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => 'Paternity Leave', 'description' => 'Leave benefit for fathers.', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => 'Special Privilege Leave', 'description' => 'Three (3) days special leave privilege for employees.', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => 'Mandatory/Forced Leave', 'description' => 'Standard CS Form 6 Leave Type', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => 'Solo Parent Leave', 'description' => 'Standard CS Form 6 Leave Type', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => '10-Day VAWC Leave', 'description' => 'Standard CS Form 6 Leave Type', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => 'Rehabilitation Privilege', 'description' => 'Standard CS Form 6 Leave Type', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => 'Special Leave Benefits for Women', 'description' => 'Standard CS Form 6 Leave Type', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => 'Special Emergency (Calamity) Leave', 'description' => 'Standard CS Form 6 Leave Type', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => 'Adoption Leave', 'description' => 'Standard CS Form 6 Leave Type', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => 'Study Leave', 'description' => 'Standard CS Form 6 Leave Type', 'created_at' => now(), 'updated_at' => now()],
            ['type_name' => 'Others', 'description' => 'Other types of leave not listed above', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Leave Applications Table
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained('leave_types');
            $table->date('date_filing')->default(now());
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_applied');
            $table->string('commutation')->default('Not Requested'); // 'Requested', 'Not Requested'
            $table->string('status')->default('Pending'); // 'Pending', 'Approved', 'Disapproved'
            $table->timestamps();
        });

        // 3. Leave Details Form 6 Table (Extension for specific details)
        Schema::create('leave_details_form6', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_application_id')->constrained('leave_applications')->onDelete('cascade');
            $table->string('leave_type_name')->nullable(); // Snapshot of type name
            
            // Vacation Details
            $table->string('vacation_loc_type')->nullable(); // 'Philippines', 'Abroad'
            $table->string('vacation_loc_details')->nullable();
            
            // Sick Details
            $table->string('sick_loc_type')->nullable(); // 'Hospital', 'Out Patient'
            $table->string('sick_illness')->nullable();
            
            // Women
            $table->string('women_illness')->nullable();
            
            // Study
            $table->string('study_type')->nullable(); // 'Masters', 'Bar'
            $table->string('study_details')->nullable();
            
            // Other
            $table->string('other_purpose')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_details_form6');
        Schema::dropIfExists('leave_applications');
        Schema::dropIfExists('leave_types');
    }
};
