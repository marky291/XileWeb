<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::query()->orderByDesc('created_at');

        if ($request->filled('client')) {
            $query->where('client', $request->client);
        }

        $posts = $query->paginate(12);

        return view('posts.index', [
            'posts' => $posts,
            'selectedClient' => $request->client,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->increment('views');

        // Previous and next posts
        $previousPost = Post::where('created_at', '<', $post->created_at)
            ->orderByDesc('created_at')
            ->first();

        $nextPost = Post::where('created_at', '>', $post->created_at)
            ->orderBy('created_at')
            ->first();

        // Related posts (same client, excluding current)
        $relatedPosts = Post::where('client', $post->client)
            ->where('id', '!=', $post->id)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        // If not enough related posts, fill with latest from other client
        if ($relatedPosts->count() < 3) {
            $moreNeeded = 3 - $relatedPosts->count();
            $morePosts = Post::where('id', '!=', $post->id)
                ->whereNotIn('id', $relatedPosts->pluck('id'))
                ->orderByDesc('created_at')
                ->limit($moreNeeded)
                ->get();
            $relatedPosts = $relatedPosts->concat($morePosts);
        }

        return view('post', [
            'post' => $post,
            'previousPost' => $previousPost,
            'nextPost' => $nextPost,
            'relatedPosts' => $relatedPosts,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
