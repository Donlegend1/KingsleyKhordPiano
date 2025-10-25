<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateCourseCategoryIds extends Command
{
    protected $signature = 'courses:update-category-ids';
    protected $description = 'Update course_category_id based on existing category and level';

    public function handle()
    {
        $this->info('Starting to update course category IDs...');

        // Get all courses that need updating
        DB::table('courses')
            ->whereNull('course_category_id')
            ->orderBy('id')
            ->chunk(100, function ($courses) {
                foreach ($courses as $course) {
                    // Find matching category
                    $category = DB::table('course_categories')
                        ->where('category', $course->category)
                        ->where('level', $course->level)
                        ->first();

                    if ($category) {
                        DB::table('courses')
                            ->where('id', $course->id)
                            ->update(['course_category_id' => $category->id]);
                        
                        $this->info("Updated course ID {$course->id} with category ID {$category->id}");
                    } else {
                        $this->warn("No matching category found for course ID {$course->id}");
                    }
                }
            });

        $this->info('Finished updating course category IDs');
    }
}
