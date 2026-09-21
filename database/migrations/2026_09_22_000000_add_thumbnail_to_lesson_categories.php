<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('piano_exercise_categories', function (Blueprint $table) {
            $table->string('thumbnail')->nullable()->after('position');
        });

        Schema::table('musical_application_categories', function (Blueprint $table) {
            $table->string('thumbnail')->nullable()->after('position');
        });
    }

    public function down(): void
    {
        Schema::table('piano_exercise_categories', function (Blueprint $table) {
            $table->dropColumn('thumbnail');
        });

        Schema::table('musical_application_categories', function (Blueprint $table) {
            $table->dropColumn('thumbnail');
        });
    }
};
