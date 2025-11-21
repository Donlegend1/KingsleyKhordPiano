<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            DB::statement('ALTER TABLE users CHANGE address country VARCHAR(255)');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            DB::statement('ALTER TABLE users CHANGE country address VARCHAR(255)');
        });
    }
};

