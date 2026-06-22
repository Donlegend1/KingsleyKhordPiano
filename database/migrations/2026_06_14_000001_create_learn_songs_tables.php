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
        if (! Schema::hasTable('learn_song_categories') && ! Schema::hasTable('learn_songs')) {

            Schema::create('learn_song_categories', function (Blueprint $table) {
                $table->id();
                $table->string('category');
                $table->string('level')->default('beginner');
                $table->integer('position')->default(0);
                $table->timestamps();
            });

            Schema::create('learn_songs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('learn_song_category_id')->constrained('learn_song_categories')->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('video_type')->default('iframe');
                $table->text('video_url');
                $table->string('thumbnail')->nullable();
                $table->string('level')->default('beginner');
                $table->string('status')->default('active');
                $table->integer('position')->default(0);
                $table->json('related_songs')->nullable();
                $table->timestamps();
            });

            // Migrate existing uploads under 'learn songs'
            try {
                $existing = DB::table('uploads')->where('category', 'learn songs')->get();
                $ids = [];
                foreach ($existing as $upload) {
                    $level = strtolower($upload->level ?: 'beginner');
                    if (!in_array($level, ['beginner', 'intermediate', 'advanced'])) {
                        $level = 'beginner';
                    }
                    $categoryName = $upload->series ?: 'General Songs';

                    // Find or create category
                    $category = DB::table('learn_song_categories')
                        ->where('category', $categoryName)
                        ->where('level', $level)
                        ->first();

                    if (!$category) {
                        $maxPos = DB::table('learn_song_categories')->where('level', $level)->max('position') ?: 0;
                        $categoryId = DB::table('learn_song_categories')->insertGetId([
                            'category' => $categoryName,
                            'level' => $level,
                            'position' => $maxPos + 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        $categoryId = $category->id;
                    }

                    $maxSongPos = DB::table('learn_songs')->where('learn_song_category_id', $categoryId)->max('position') ?: 0;

                    $related = null;
                    if (isset($upload->tags) && $upload->tags) {
                        $related = $upload->tags;
                    }

                    DB::table('learn_songs')->insert([
                        'id' => $upload->id, // Preserve ID
                        'learn_song_category_id' => $categoryId,
                        'title' => $upload->title,
                        'description' => $upload->description,
                        'video_type' => isset($upload->video_type) ? $upload->video_type : 'iframe',
                        'video_url' => $upload->video_url,
                        'thumbnail' => $upload->thumbnail,
                        'level' => $level,
                        'status' => $upload->status ?: 'active',
                        'position' => $maxSongPos + 1,
                        'related_songs' => $related,
                        'created_at' => $upload->created_at ?: now(),
                        'updated_at' => $upload->updated_at ?: now(),
                    ]);

                    $ids[] = $upload->id;
                }

                // Update bookmarks
                if (count($ids)) {
                    DB::table('bookmarks')
                        ->where('bookmarkable_type', 'App\Models\Upload')
                        ->whereIn('bookmarkable_id', $ids)
                        ->update(['bookmarkable_type' => 'App\Models\LearnSong']);

                    // Delete from uploads table to prevent duplicates
                    DB::table('uploads')->whereIn('id', $ids)->delete();
                }
            } catch (\Exception $e) {
                logger()->error('Error migrating learn songs: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learn_songs');
        Schema::dropIfExists('learn_song_categories');
    }
};