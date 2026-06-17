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
        Schema::create('extra_course_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('level')->default('beginner');
            $table->integer('position')->default(0);
            $table->timestamps();
        });

        Schema::create('extra_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extra_course_category_id')->constrained('extra_course_categories')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('video_type')->default('iframe');
            $table->text('video_url');
            $table->string('thumbnail')->nullable();
            $table->string('level')->default('beginner');
            $table->string('status')->default('active');
            $table->integer('position')->default(0);
            $table->json('related_courses')->nullable();
            $table->timestamps();
        });

        // Migrate existing uploads under 'extra courses'
        try {
            $existing = DB::table('uploads')->where('category', 'extra courses')->get();
            $ids = [];
            foreach ($existing as $upload) {
                $level = strtolower($upload->level ?: 'beginner');
                if (!in_array($level, ['beginner', 'intermediate', 'advanced'])) {
                    $level = 'beginner';
                }
                $categoryName = $upload->series ?: 'General Extra Courses';

                // Find or create category
                $category = DB::table('extra_course_categories')
                    ->where('category', $categoryName)
                    ->where('level', $level)
                    ->first();

                if (!$category) {
                    $maxPos = DB::table('extra_course_categories')->where('level', $level)->max('position') ?: 0;
                    $categoryId = DB::table('extra_course_categories')->insertGetId([
                        'category' => $categoryName,
                        'level' => $level,
                        'position' => $maxPos + 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $categoryId = $category->id;
                }

                $maxCoursePos = DB::table('extra_courses')->where('extra_course_category_id', $categoryId)->max('position') ?: 0;
                
                $related = null;
                if (isset($upload->tags) && $upload->tags) {
                    $related = $upload->tags;
                }

                DB::table('extra_courses')->insert([
                    'id' => $upload->id, // Preserve ID
                    'extra_course_category_id' => $categoryId,
                    'title' => $upload->title,
                    'description' => $upload->description,
                    'video_type' => isset($upload->video_type) ? $upload->video_type : 'iframe',
                    'video_url' => $upload->video_url,
                    'thumbnail' => $upload->thumbnail,
                    'level' => $level,
                    'status' => $upload->status ?: 'active',
                    'position' => $maxCoursePos + 1,
                    'related_courses' => $related,
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
                    ->update(['bookmarkable_type' => 'App\Models\ExtraCourse']);

                // Delete from uploads table to prevent duplicates
                DB::table('uploads')->whereIn('id', $ids)->delete();
            }
        } catch (\Exception $e) {
            logger()->error('Error migrating extra courses: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extra_courses');
        Schema::dropIfExists('extra_course_categories');
    }
};
