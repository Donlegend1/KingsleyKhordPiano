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
        Schema::create('shop_products', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['midi', 'plugin']);
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            $table->decimal('regular_price', 8, 2);
            $table->decimal('sale_price', 8, 2)->nullable();
            $table->string('download_url')->nullable();
            $table->string('video_url')->nullable();
            $table->text('system_requirements')->nullable();
            // Fallback "cover art" gradient shown when no thumbnail has been uploaded yet.
            $table->string('gradient_from', 7)->default('#1c1c1c');
            $table->string('gradient_to', 7)->default('#050505');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_products');
    }
};
