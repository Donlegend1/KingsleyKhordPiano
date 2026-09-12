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
            // The name shown across the community (posts, replies, profile) —
            // independent of first_name/last_name. Falls back to full_name
            // when not set (see User::getDisplayNameAttribute()).
            $table->string('display_name')->nullable()->after('last_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('display_name');
        });
    }
};
