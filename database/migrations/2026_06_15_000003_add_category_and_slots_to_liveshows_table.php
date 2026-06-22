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
        Schema::table('liveshows', function (Blueprint $table) {
            if (! Schema::hasColumn('liveshows', 'category')) {
                $table->string('category')->default('event'); // 'event' or 'session'
            }

            if (! Schema::hasColumn('liveshows', 'max_slots')) {
                $table->integer('max_slots')->default(5);
            }
        });

        if (! Schema::hasTable('liveshow_bookings')) {
            Schema::create('liveshow_bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('liveshow_id')->constrained('liveshows')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['liveshow_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liveshow_bookings');

        Schema::table('liveshows', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('liveshows', 'category')) {
                $columnsToDrop[] = 'category';
            }

            if (Schema::hasColumn('liveshows', 'max_slots')) {
                $columnsToDrop[] = 'max_slots';
            }

            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};