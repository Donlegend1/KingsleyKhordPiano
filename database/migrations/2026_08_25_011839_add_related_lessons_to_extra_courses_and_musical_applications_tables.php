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
        Schema::table('extra_courses', function (Blueprint $table) {
            $table->json('related_lessons')->nullable()->after('related_courses');
        });

        Schema::table('musical_applications', function (Blueprint $table) {
            $table->json('related_lessons')->nullable()->after('duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extra_courses', function (Blueprint $table) {
            $table->dropColumn('related_lessons');
        });

        Schema::table('musical_applications', function (Blueprint $table) {
            $table->dropColumn('related_lessons');
        });
    }
};
