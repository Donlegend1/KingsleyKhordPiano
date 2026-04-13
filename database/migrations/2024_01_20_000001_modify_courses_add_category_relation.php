<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('courses')) {
            return;
        }

        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'category')) {
                $table->string('category')->nullable()->change();
            }

            if (! Schema::hasColumn('courses', 'course_category_id')) {
                $table->foreignId('course_category_id')
                    ->nullable()
                    ->after('category');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('courses')) {
            return;
        }

        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'category')) {
                $table->string('category')->nullable(false)->change();
            }

            if (Schema::hasColumn('courses', 'course_category_id')) {
                $table->dropColumn('course_category_id');
            }
        });
    }
};
