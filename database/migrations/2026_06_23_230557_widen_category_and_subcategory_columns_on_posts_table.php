<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE posts MODIFY category VARCHAR(50) NOT NULL");
        DB::statement("ALTER TABLE posts MODIFY subcategory VARCHAR(50) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE posts MODIFY category ENUM('get_started','others','forum') NOT NULL");
        DB::statement("ALTER TABLE posts MODIFY subcategory ENUM('say_hello','ask_question','post_progress','beginner','intermediate','advance','lessons') NOT NULL");
    }
};
