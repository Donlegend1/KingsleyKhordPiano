<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseCategory;

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
}
