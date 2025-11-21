<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            // Make category nullable
            $table->string('category')->nullable()->change();
            
            // Add foreign key
            $table->foreignId('course_category_id')
                  ->nullable()
                  ->after('category');
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('category')->nullable(false)->change();
            $table->dropForeign(['course_category_id']);
            $table->dropColumn('course_category_id');
        });
    }
};
