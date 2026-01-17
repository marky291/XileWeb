<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $posts = Post::orderByDesc('updated_at')->get();

        $content = view('sitemap.index', [
            'posts' => $posts,
        ])->render();

        return response($content)
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $content = view('sitemap.robots')->render();

        return response($content)
            ->header('Content-Type', 'text/plain');
    }
}
