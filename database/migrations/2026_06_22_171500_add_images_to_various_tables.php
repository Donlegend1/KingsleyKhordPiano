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
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (!Schema::hasColumn('courses', 'thumbnail')) {
                    $table->string('thumbnail')->nullable()->after('video_url');
                }
                $table->json('images')->nullable()->after('thumbnail');
            });
        }
        if (Schema::hasTable('extra_courses')) {
            Schema::table('extra_courses', function (Blueprint $table) {
                $table->json('images')->nullable()->after('thumbnail');
            });
        }
        if (Schema::hasTable('learn_songs')) {
            Schema::table('learn_songs', function (Blueprint $table) {
                $table->json('images')->nullable()->after('thumbnail');
            });
        }
        if (Schema::hasTable('uploads')) {
            Schema::table('uploads', function (Blueprint $table) {
                $table->json('images')->nullable()->after('thumbnail');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('courses')) {
            Schema::table('courses', function (Blueprint $table) {
                if (Schema::hasColumn('courses', 'thumbnail')) {
                    $table->dropColumn('thumbnail');
                }
                $table->dropColumn('images');
            });
        }
        if (Schema::hasTable('extra_courses')) {
            Schema::table('extra_courses', function (Blueprint $table) {
                $table->dropColumn('images');
            });
        }
        if (Schema::hasTable('learn_songs')) {
            Schema::table('learn_songs', function (Blueprint $table) {
                $table->dropColumn('images');
            });
        }
        if (Schema::hasTable('uploads')) {
            Schema::table('uploads', function (Blueprint $table) {
                $table->dropColumn('images');
            });
        }
    }
};
