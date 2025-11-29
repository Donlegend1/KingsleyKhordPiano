<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseCategory;
use App\Models\Course;

class CourseCategoryController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'level' => 'required|string|max:255',
        ]);

        $courseCategory = CourseCategory::create([
            'category' => $request->input('category'),
            'level' => $request->input('level'),
            'position' => CourseCategory::max('position') + 1,
        ]);

        return response()->json($courseCategory, 201);
    }   

    public function delete($name)
    {
        $category = CourseCategory::where('category', $name)->first();
        $existingCourse = Course::where('course_category_id', $category->id)->first();

        if ($existingCourse) {
            return response()->json([
                'message' => 'Cannot delete category because it has courses assigned.'
            ], 400);
        }

        // Safe to delete
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ], 200);
    }
}
