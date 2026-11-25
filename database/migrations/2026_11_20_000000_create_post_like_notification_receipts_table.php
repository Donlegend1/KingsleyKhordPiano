<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_like_notification_receipts', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['post_id', 'user_id']);
        });

        DB::table('post_like_notification_receipts')->insertUsing(
            ['post_id', 'user_id'],
            DB::table('post_likes')->select('post_id', 'user_id')->distinct(),
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('post_like_notification_receipts');
    }
};
