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
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->foreignId('asds_id')->nullable()->after('approving_officer_id')->constrained('users')->onDelete('set null');
            $table->timestamp('asds_approved_at')->nullable()->after('recommended_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_applications', function (Blueprint $table) {
            $table->dropForeign(['asds_id']);
            $table->dropColumn(['asds_id', 'asds_approved_at']);
        });
    }
};
