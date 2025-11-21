<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostMedia;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Community;

class PostController extends Controller
{

    public function index(Request $request)
    {
        $query = Post::with([
            'comments.user',
            'comments.replies.user',
            'likes.user',
            'user',
            'media'
        ]);

        if ($request->filled('subcategory')) {
            $subcategory = Str::replace('-', '_', $request->get('subcategory'));
            $query->where('subcategory', $subcategory);
        }

         if ($request->filled('post_id')) {
            $query->where('id', $request->post_id);
        }

        // Sorting logic
        switch ($request->get('sort')) {
            case 'old':
                $query->oldest();
                break;
            case 'popular':
                $query->withCount('comments')->orderByDesc('comments_count');
                break;
            case 'likes':
                $query->withCount('likes')->orderByDesc('likes_count');
                break;
            case 'latest':
            default:
                $query->latest();
        }

        return response()->json($query->paginate(5));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StorePostRequest $request)
    {
        $userId = auth()->id() ?? 1;

        $post = Post::create([
            'title' => $request->title,
            'body' => $request->body,
            'category' => $request->category,
            'subcategory' => $request->subcategory,
            'user_id' => $userId,
        ]);

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                $type = in_array($extension, ['mp4', 'mov', 'avi']) ? 'video' : 'image';

                // Generate unique file name
                $fileName = Str::uuid() . '.' . $extension;

                // Move file to public/uploads/posts
                $file->move(public_path('uploads/posts'), $fileName);

                // Save in post_media table
                PostMedia::create([
                    'post_id' => $post->id,
                    'file_path' => 'uploads/posts/' . $fileName,
                    'type' => $type,
                ]);
            }
        }

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post->load('media'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $validatedData = $request->validated();
        $post->update($validatedData);
        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $user = auth()->user();

        if ($post->user_id !== $user->id && $user->email !== 'kingsleykhord@gmail.com') {
            return response()->json([
                'message' => 'You are not authorized to delete this post.'
            ], 403);
        }

        foreach ($post->comments as $comment) {
            $comment->replies()->delete();
            $comment->delete();
        }

        $post->likes()->delete();

        // Delete media files from server
        foreach ($post->media as $media) {
            $filePath = public_path($media->file_path);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            $media->delete();
        }

        // Delete the post
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully.'
        ], 200);
    }


    public function postByUser(Request $request, Community $community)
    {
         $query = Post::where('user_id', $community->user_id)->with([
            'comments.user',          
            'comments.replies.user',  
            'likes.user',             
            'user' ,                 
            'media'
        ]);

        switch ($request->get('sort')) {
            case 'old':
                $query->oldest();
                break;
            case 'popular':
                $query->withCount('comments')->orderByDesc('comments_count');
                break;
            case 'likes':
                $query->withCount('likes')->orderByDesc('likes_count');
                break;
            case 'latest':
            default:
                $query->latest();
        }

        return response()->json($query->paginate(5));
    }
}
