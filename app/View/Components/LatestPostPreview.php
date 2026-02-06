<?php

namespace App\View\Components;

use App\Models\Post;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class LatestPostPreview extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.latest-post-preview', [
            'posts' => Cache::remember('homepage:latest-posts', now()->addHour(), function () {
                return Post::latest()->take(3)->get();
            }),
        ]);
    }
}
