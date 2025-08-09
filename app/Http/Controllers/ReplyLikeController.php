<?php

namespace App\Http\Controllers;

use App\Models\ReplyLike;
use App\Http\Requests\StoreReplyLikeRequest;
use App\Http\Requests\UpdateReplyLikeRequest;

class ReplyLikeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $replyLikes = ReplyLike::all();
        return view('reply_likes.index', compact('replyLikes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('reply_likes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReplyLikeRequest $request)
    {
        $validatedData = $request->validated();
        ReplyLike::create($validatedData);
        return redirect()->route('reply_likes.index')->with('success', 'Reply Like created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReplyLike $replyLike)
    {
        return view('reply_likes.show', compact('replyLike'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ReplyLike $replyLike)
    {
        return view('reply_likes.edit', compact('replyLike'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReplyLikeRequest $request, ReplyLike $replyLike)
    {
        $validatedData = $request->validated();
        $replyLike->update($validatedData);
        return redirect()->route('reply_likes.index')->with('success', 'Reply Like updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReplyLike $replyLike)
    {
        $replyLike->delete();
        return redirect()->route('reply_likes.index')->with('success', 'Reply Like deleted successfully.');
    }
}
