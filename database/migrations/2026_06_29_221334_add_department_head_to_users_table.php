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
            $table->foreignId('department_head_id')->nullable()->constrained('users')->onDelete('set null');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->string('link_url')->nullable()->after('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_head_id']);
            $table->dropColumn('department_head_id');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('link_url');
        });
    }
};
