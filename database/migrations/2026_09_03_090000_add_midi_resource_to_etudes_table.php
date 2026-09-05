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
        Schema::table('etudes', function (Blueprint $table) {
            $table->string('midi_resource')->nullable()->after('pdf_resource');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etudes', function (Blueprint $table) {
            $table->dropColumn('midi_resource');
        });
    }
};
