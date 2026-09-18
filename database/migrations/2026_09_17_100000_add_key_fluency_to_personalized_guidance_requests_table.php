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
        Schema::table('personalized_guidance_requests', function (Blueprint $table) {
            $table->string('key_fluency')->nullable()->after('chord_vocabulary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personalized_guidance_requests', function (Blueprint $table) {
            $table->dropColumn('key_fluency');
        });
    }
};
