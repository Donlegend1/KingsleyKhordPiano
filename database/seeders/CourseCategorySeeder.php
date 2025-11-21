<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseCategorySeeder extends Seeder
{
    public function run()
    {
        // Get unique categories from courses table grouped by level
        $categories = DB::table('courses')
            ->select('category', 'level')
            ->distinct()
            ->get();

        // Insert categories with positions
        foreach ($categories as $index => $cat) {
            DB::table('course_categories')->insert([
                'category' => $cat->category,
                'level' => $cat->level,
                'position' => $index,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
