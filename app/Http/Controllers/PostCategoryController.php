<?php

namespace App\Http\Controllers;

use App\Models\PostCategory;
use App\Http\Requests\StorePostCategoryRequest;
use App\Http\Requests\UpdatePostCategoryRequest;

class PostCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $postCategories = PostCategory::all();
        return view('post_categories.index', compact('postCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostCategoryRequest $request)
    {
        $validatedData = $request->validated();
        PostCategory::create($validatedData);
        return redirect()->route('post_categories.index')->with('success', 'Post Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PostCategory $postCategory)
    {
        return view('post_categories.show', compact('postCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PostCategory $postCategory)
    {
        return view('post_categories.edit', compact('postCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostCategoryRequest $request, PostCategory $postCategory)
    {
        $validatedData = $request->validated();
        $postCategory->update($validatedData);
        return redirect()->route('post_categories.index')->with('success', 'Post Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostCategory $postCategory)
    {
        $postCategory->delete();
        return redirect()->route('post_categories.index')->with('success', 'Post Category deleted successfully.');
    }
}
