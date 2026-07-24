<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('uploads')->where('category', 'quick lessons')->delete();
        DB::statement("ALTER TABLE uploads MODIFY category ENUM('piano exercise', 'extra courses', 'learn songs')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE uploads MODIFY category ENUM('piano exercise', 'extra courses', 'quick lessons', 'learn songs')");
    }
};
