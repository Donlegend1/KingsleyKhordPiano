<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'address') || Schema::hasColumn('users', 'country')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('address', 'country');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'country') || Schema::hasColumn('users', 'address')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('country', 'address');
        });
    }
};
