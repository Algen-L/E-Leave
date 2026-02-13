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
            $table->foreignId('recommending_officer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approving_officer_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['recommending_officer_id']);
            $table->dropForeign(['approving_officer_id']);
            $table->dropColumn(['recommending_officer_id', 'approving_officer_id']);
        });
    }
};
