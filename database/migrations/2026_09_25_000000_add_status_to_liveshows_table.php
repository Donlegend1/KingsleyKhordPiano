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
        Schema::table('liveshows', function (Blueprint $table) {
            if (! Schema::hasColumn('liveshows', 'status')) {
                $table->string('status')->default('open'); // 'open' or 'coming_soon'
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('liveshows', function (Blueprint $table) {
            if (Schema::hasColumn('liveshows', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
