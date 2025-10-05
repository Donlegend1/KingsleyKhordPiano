<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;

class LessonController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the lessons page.
     *
     * @return \Illuminate\View\View
     */
    public function quicklession(Request $request) 
    {
        $allPage = $request->input('all_page', 1);
        $beginnerPage = $request->input('beginner_page', 1);
        $intermediatePage = $request->input('intermediate_page', 1);
        $advancedPage = $request->input('advanced_page', 1);
        $search = $request->input('search');

        $allQuery = Upload::where('category', 'quick lessons')->latest();
        $beginnerQuery = Upload::where('category', 'quick lessons')->where('level', 'Beginner')->latest();
        $intermediateQuery = Upload::where('category', 'quick lessons')->where('level', 'Intermediate')->latest();
        $advancedQuery = Upload::where('category', 'quick lessons')->where('level', 'Advanced')->latest();

        if ($search) {
            $allQuery->where('title', 'like', "%{$search}%");
            $beginnerQuery->where('title', 'like', "%{$search}%");
            $intermediateQuery->where('title', 'like', "%{$search}%");
            $advancedQuery->where('title', 'like', "%{$search}%");
        }

        $all = $allQuery->paginate(9, ['*'], 'all_page', $allPage);
        $beginner = $beginnerQuery->paginate(9, ['*'], 'beginner_page', $beginnerPage);
        $intermediate = $intermediateQuery->paginate(9, ['*'], 'intermediate_page', $intermediatePage);
        $advanced = $advancedQuery->paginate(9, ['*'], 'advanced_page', $advancedPage);

        return view('memberpages.quicklesson', compact('all', 'beginner', 'intermediate', 'advanced', 'search'));
    }

    public function learnSongs(Request $request)
    {
        $allPage = $request->input('all_page', 1);
        $beginnerPage = $request->input('beginner_page', 1);
        $intermediatePage = $request->input('intermediate_page', 1);
        $advancedPage = $request->input('advanced_page', 1);
        $search = $request->input('search');

        $allQuery = Upload::where('category', 'learn songs')->latest();
        $beginnerQuery = Upload::where('category', 'learn songs')->where('level', 'Beginner')->latest();
        $intermediateQuery = Upload::where('category', 'learn songs')->where('level', 'Intermediate')->latest();
        $advancedQuery = Upload::where('category', 'learn songs')->where('level', 'Advanced')->latest();

        if ($search) {
            $allQuery->where('title', 'like', "%{$search}%");
            $beginnerQuery->where('title', 'like', "%{$search}%");
            $intermediateQuery->where('title', 'like', "%{$search}%");
            $advancedQuery->where('title', 'like', "%{$search}%");
        }

        $all = $allQuery->paginate(9, ['*'], 'all_page', $allPage);
        $beginner = $beginnerQuery->paginate(9, ['*'], 'beginner_page', $beginnerPage);
        $intermediate = $intermediateQuery->paginate(9, ['*'], 'intermediate_page', $intermediatePage);
        $advanced = $advancedQuery->paginate(9, ['*'], 'advanced_page', $advancedPage);

        return view('memberpages.learnsongs', compact('all', 'beginner', 'intermediate', 'advanced', 'search'));
    }
}
