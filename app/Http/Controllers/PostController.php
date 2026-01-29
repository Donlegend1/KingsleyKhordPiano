<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostMedia;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Community;
use App\Helpers\VideoHelper;
use App\Models\PostBlock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PostController extends Controller
{

    public function index(Request $request)
    {
        $query = Post::where('subcategory', '!=', 'exclusive_feed')->with([
            'comments.user',
            'comments.replies.user',
            'likes.user',
            'user',
            'media',
            'blocks'
        ]);

        if ($request->filled('subcategory')) {
            $subcategory = Str::replace('-', '_', $request->get('subcategory'));
            $query->where('subcategory', $subcategory);
        }

         if ($request->filled('post_id')) {
            $query->where('id', $request->post_id);
        }

        $query->orderByDesc('is_pinned');

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
                $query->orderByDesc('updated_at');
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
        DB::transaction(function () use ($request) {
        $post = Post::create([
            'title' => $request->title,
            'user_id' => auth()->id(),
            'category' => $request->category,
            'subcategory' => $request->subcategory,
        ]);
        logger()->info(['blocks' => $request->blocks]);

       foreach ($request->blocks as $index => $block) {
            $data = [
                'post_id' => $post->id,
                'type' => $block['type'],
                'position' => $index,
            ];

            if ($block['type'] === 'text') {
                $data['content'] = $block['content'];
            }

            if ($block['type'] === 'link') {
                $data['content'] = $block['content'];
                // Convert YouTube/Vimeo links to embed URL
                $data['embed_url'] = VideoHelper::linkToEmbed($block['content']);
            }

            if (in_array($block['type'], ['image', 'video', 'audio'])) {
                if ($request->hasFile($block['content'])) {
                    $file = $request->file($block['content']);
                    $path = $file->store('posts', 'public');
                    $data['content'] = $path;
                }
            }

            PostBlock::create($data);
        }

        // Return after all blocks are saved
        return response()->json([
            'message' => 'Post created successfully',
            'post_id' => $post->id,
        ], 201);
        });
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
            'media',
            'blocks'
        ]);

        $query->orderByDesc('is_pinned');

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
                $query->orderByDesc('updated_at');
        }

        return response()->json($query->paginate(5));
    }

    public function exclusiveField(Request $request)
    {
        $query = Post::where('subcategory', 'exclusive_feed')->with([
            'comments.user',
            'comments.replies.user',
            'likes.user',
            'user',
            'media',
            'blocks'
        ]);

        $query->orderByDesc('is_pinned');

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
                $query->orderByDesc('updated_at');
        }

        return response()->json($query->paginate(5));
    }

    public function togglePin(Post $post)
    {
        $user = auth()->user();

        // if ($user->email !== 'kingsleykhord@gmail.com') {
        //     return response()->json([
        //         'message' => 'You are not authorized to pin this post'
        //     ], 403);
        // }

        $post->update([
            'is_pinned' => ! $post->is_pinned,
        ]);

        return response()->json([
            'message' => $post->is_pinned ? 'Post pinned' : 'Post unpinned',
            'is_pinned' => $post->is_pinned,
        ]);
    }
}

