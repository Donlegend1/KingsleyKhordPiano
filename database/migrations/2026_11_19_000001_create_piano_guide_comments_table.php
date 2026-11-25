<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('piano_guide_comments', function (Blueprint $table) {
            $table->id();
            $table->string('guide_slug', 100);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('piano_guide_comments')->cascadeOnDelete();
            $table->text('body')->nullable();
            $table->timestamps();
            $table->index(['guide_slug', 'parent_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('piano_guide_comments');
    }
};
