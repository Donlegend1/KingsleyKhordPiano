<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('musical_application_categories')) {
            Schema::create('musical_application_categories', function (Blueprint $table) {
                $table->id();
                $table->string('category');
                $table->string('level')->default('beginner');
                $table->integer('position')->default(0);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('musical_applications') && ! Schema::hasColumn('musical_applications', 'musical_application_category_id')) {
            Schema::table('musical_applications', function (Blueprint $table) {
                $table->foreignId('musical_application_category_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('musical_application_categories')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasTable('piano_exercise_categories')) {
            Schema::create('piano_exercise_categories', function (Blueprint $table) {
                $table->id();
                $table->string('category');
                $table->string('level')->default('independence');
                $table->integer('position')->default(0);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('uploads') && ! Schema::hasColumn('uploads', 'piano_exercise_category_id')) {
            Schema::table('uploads', function (Blueprint $table) {
                $table->foreignId('piano_exercise_category_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('piano_exercise_categories')
                    ->nullOnDelete();
            });
        }

        $this->migrateMusicalApplications();
        $this->migratePianoExercises();
    }

    public function down(): void
    {
        if (Schema::hasTable('uploads') && Schema::hasColumn('uploads', 'piano_exercise_category_id')) {
            Schema::table('uploads', function (Blueprint $table) {
                $table->dropConstrainedForeignId('piano_exercise_category_id');
            });
        }

        if (Schema::hasTable('musical_applications') && Schema::hasColumn('musical_applications', 'musical_application_category_id')) {
            Schema::table('musical_applications', function (Blueprint $table) {
                $table->dropConstrainedForeignId('musical_application_category_id');
            });
        }

        Schema::dropIfExists('piano_exercise_categories');
        Schema::dropIfExists('musical_application_categories');
    }

    private function migrateMusicalApplications(): void
    {
        if (! Schema::hasTable('musical_applications')) {
            return;
        }

        $rows = DB::table('musical_applications')->orderBy('id')->get();
        foreach ($rows as $row) {
            $level = strtolower((string) ($row->skill_level ?: 'beginner'));
            if (! in_array($level, ['beginner', 'intermediate', 'advanced'], true)) {
                $level = 'beginner';
            }
            $categoryName = trim((string) ($row->series ?: 'General'));
            $categoryId = $this->findOrCreateCategory('musical_application_categories', $categoryName, $level);

            DB::table('musical_applications')->where('id', $row->id)->update([
                'musical_application_category_id' => $categoryId,
                'series' => $categoryName,
            ]);
        }
    }

    private function migratePianoExercises(): void
    {
        if (! Schema::hasTable('uploads')) {
            return;
        }

        $rows = DB::table('uploads')->where('category', 'piano exercise')->orderBy('id')->get();
        foreach ($rows as $row) {
            $level = strtolower((string) ($row->level ?: 'independence'));
            $allowed = ['independence', 'technique', 'flexibility', 'strength', 'dexterity'];
            if (! in_array($level, $allowed, true)) {
                $level = 'independence';
            }
            $categoryName = trim((string) ($row->series ?: 'General'));
            $categoryId = $this->findOrCreateCategory('piano_exercise_categories', $categoryName, $level);

            DB::table('uploads')->where('id', $row->id)->update([
                'piano_exercise_category_id' => $categoryId,
                'series' => $categoryName,
                'level' => $level,
            ]);
        }
    }

    private function findOrCreateCategory(string $table, string $name, string $level): int
    {
        $existing = DB::table($table)
            ->where('category', $name)
            ->where('level', $level)
            ->first();

        if ($existing) {
            return $existing->id;
        }

        $maxPos = (int) (DB::table($table)->where('level', $level)->max('position') ?: 0);

        return DB::table($table)->insertGetId([
            'category' => $name,
            'level' => $level,
            'position' => $maxPos + 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
