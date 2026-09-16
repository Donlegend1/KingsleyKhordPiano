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
        if (! Schema::hasColumn('courses', 'position')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->string('position')->nullable()->after('course_category_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('courses', 'position')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('position');
            });
        }
    }
};
