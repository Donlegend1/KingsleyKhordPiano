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
            $table->string('inspiration_pianist')->nullable()->after('key_fluency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personalized_guidance_requests', function (Blueprint $table) {
            $table->dropColumn('inspiration_pianist');
        });
    }
};
