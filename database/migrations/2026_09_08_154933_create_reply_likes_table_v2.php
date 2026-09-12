<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reply_likes')) {
            Schema::create('reply_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_reply_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['post_reply_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reply_likes');
    }
};
