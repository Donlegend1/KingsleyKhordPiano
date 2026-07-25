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
        Schema::table('live_coaching_bookings', function (Blueprint $table) {
            $table->string('zoom_join_url')->nullable()->after('time');
            $table->string('zoom_meeting_id')->nullable()->after('zoom_join_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_coaching_bookings', function (Blueprint $table) {
            $table->dropColumn(['zoom_join_url', 'zoom_meeting_id']);
        });
    }
};
