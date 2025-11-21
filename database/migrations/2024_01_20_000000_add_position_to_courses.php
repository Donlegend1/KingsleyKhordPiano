<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('level')->default('beginner');
            $table->integer('position');
            $table->timestamps();
            $table->unique(['category', 'level', 'position']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_categories');
    }
};
