<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('topic_follows')) {
            Schema::create('topic_follows', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('subcategory');
                $table->timestamps();

                $table->unique(['user_id', 'subcategory']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('topic_follows');
    }
};
