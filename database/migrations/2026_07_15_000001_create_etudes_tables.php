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
        if (!Schema::hasTable('etude_categories')) {
            Schema::create('etude_categories', function (Blueprint $table) {
                $table->id();
                $table->string('category');
                $table->integer('position')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('etudes')) {
            Schema::create('etudes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('etude_category_id')->constrained('etude_categories')->onDelete('cascade');
                $table->string('title');
                $table->string('author')->nullable();
                $table->text('description')->nullable();
                $table->string('video_type')->default('iframe');
                $table->text('video_url');
                $table->string('thumbnail')->nullable();
                $table->string('status')->default('active');
                $table->integer('position')->default(0);
                $table->json('related_etudes')->nullable();
                $table->json('images')->nullable();
                $table->string('audio_resource')->nullable();
                $table->string('pdf_resource')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etudes');
        Schema::dropIfExists('etude_categories');
    }
};
