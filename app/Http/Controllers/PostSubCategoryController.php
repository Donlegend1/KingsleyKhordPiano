<?php

namespace App\Http\Controllers;

use App\Models\PostSubCategory;
use App\Http\Requests\StorePostSubCategoryRequest;
use App\Http\Requests\UpdatePostSubCategoryRequest;

class PostSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $postSubCategories = PostSubCategory::all();
        return view('post_sub_categories.index', compact('postSubCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('post_sub_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostSubCategoryRequest $request)
    {
        $validatedData = $request->validated();
        PostSubCategory::create($validatedData);
        return redirect()->route('post_sub_categories.index')->with('success', 'Post Sub Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PostSubCategory $postSubCategory)
    {
        return view('post_sub_categories.show', compact('postSubCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PostSubCategory $postSubCategory)
    {
        return view('post_sub_categories.edit', compact('postSubCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostSubCategoryRequest $request, PostSubCategory $postSubCategory)
    {
        $validatedData = $request->validated();
        $postSubCategory->update($validatedData);
        return redirect()->route('post_sub_categories.index')->with('success', 'Post Sub Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostSubCategory $postSubCategory)
    {
        $postSubCategory->delete();
        return redirect()->route('post_sub_categories.index')->with('success', 'Post Sub Category deleted successfully.');
    }
}
