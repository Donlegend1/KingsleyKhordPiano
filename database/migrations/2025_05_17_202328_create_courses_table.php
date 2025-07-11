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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category');
            $table->text('description');
            $table->text('video_url');
            $table->string('level')->default('beginner');
            $table->enum('status', ['active', 'inactive', 'draft'])->default('active');
            $table->text('prerequisites')->nullable();
            $table->text('what_you_will_learn')->nullable();
            $table->json('resources')->nullable();
            $table->text('requirements')->nullable();
            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
