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
        Schema::table('musical_applications', function (Blueprint $table) {
            $table->integer('position')->nullable()->after('status');
        });

        Schema::table('uploads', function (Blueprint $table) {
            $table->integer('position')->nullable()->after('status');
        });

        // Initialize positions with id
        \Illuminate\Support\Facades\DB::table('musical_applications')->update(['position' => \Illuminate\Support\Facades\DB::raw('id')]);
        \Illuminate\Support\Facades\DB::table('uploads')->update(['position' => \Illuminate\Support\Facades\DB::raw('id')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('musical_applications', function (Blueprint $table) {
            $table->dropColumn('position');
        });

        Schema::table('uploads', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
