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
        if (Schema::hasTable('learn_songs') && ! Schema::hasColumn('learn_songs', 'song_key')) {
            Schema::table('learn_songs', function (Blueprint $table) {
                $table->string('song_key')->nullable()->after('level');
            });
        }

        $keysByTitle = [
            'Amazing Grace - Beginner Chord Breakdown' => 'G',
            'Way Maker - Worship Piano Breakdown' => 'E',
            'You Are Good - Intermediate Groove Study' => 'A',
            'Excess Love - Gospel Voicing Breakdown' => 'F#',
            'Great Are You Lord - Advanced Worship Reharm' => 'D',
            'Total Praise - Advanced Gospel Passing Chords' => 'Db',
        ];

        foreach ($keysByTitle as $title => $key) {
            DB::table('learn_songs')
                ->where('title', $title)
                ->whereNull('song_key')
                ->update(['song_key' => $key]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('learn_songs') && Schema::hasColumn('learn_songs', 'song_key')) {
            Schema::table('learn_songs', function (Blueprint $table) {
                $table->dropColumn('song_key');
            });
        }
    }
};
