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
        Schema::table('post_media', function (Blueprint $table) {
            // Make post_id nullable
            $table->unsignedBigInteger('post_id')->nullable()->change();

            // Add premium_post_id
            $table->unsignedBigInteger('premium_post_id')->nullable()->after('post_id');

            // (Optional) If it references a premium_posts table:
            // $table->foreign('premium_post_id')->references('id')->on('premium_posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('post_media', function (Blueprint $table) {
            // Revert post_id to non-nullable
            $table->unsignedBigInteger('post_id')->nullable(false)->change();

            // Drop premium_post_id
            $table->dropColumn('premium_post_id');
        });
    }
};
