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
        Schema::table('musical_applications', function (Blueprint $table) {
            $table->string('audio_resource')->nullable()->after('thumbnail');
            $table->string('pdf_resource')->nullable()->after('audio_resource');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musical_applications', function (Blueprint $table) {
            $table->dropColumn(['audio_resource', 'pdf_resource']);
        });
    }
};
