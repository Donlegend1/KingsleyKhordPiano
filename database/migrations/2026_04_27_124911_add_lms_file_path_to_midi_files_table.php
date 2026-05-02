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
        Schema::table('midi_files', function (Blueprint $table) {
            $table->string('lms_file_path')->nullable()->after('lmv_file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('midi_files', function (Blueprint $table) {
            $table->dropColumn('lms_file_path');
        });
    }
};
