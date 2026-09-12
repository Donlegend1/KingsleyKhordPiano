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
        Schema::table('quiz_questions', function (Blueprint $table) {
            // The piano key highlighted as this question's reference note —
            // only used by "Find the Key" / "Find the key #2" style lessons.
            $table->string('reference_note')->nullable()->after('correct_option');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropColumn('reference_note');
        });
    }
};
