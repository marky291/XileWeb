<div>
    <section id="important-links" class="relative overflow-hidden py-8 bg-clash-bg px-24">
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            <h2>Latest Updates</h2>
{{--            <p class="mt-6 text-gray-300 leading-relaxed">Welcome to the world of Xilero, where unique adventures await! If you're new to our server or looking to enhance your gameplay experience, you've come to the right place. Our Getting Started guides are crafted to help players of all levels navigate the distinct features and mechanics that set Xilero apart.</p>--}}
            <div class="grid grid-cols-3 gap-12 mt-14">
                @foreach(\App\Models\Post::orderBy('created_at', 'desc')->take(3)->get() as $post)
                    <div class="col-span-1 md:col-span-2 lg:col-span-1 rounded block-home overflow-hidden relative">
                        <a title="{{ $post->title }}" href="{{ route('posts.show', $post) }}">
                            <div class="p-6 rounded-md hover:shadow-lg prose relative">
                                @if($post->image)
                                    <div class="mb-6 rounded overflow-hidden">
                                        <img class="object-cover w-full rounded h-44" style="margin:0" src="{{ Storage::disk('public')->url($post->image) }}" alt="{{ $post->title }}">
                                    </div>
                                @endif
                                <div class="flex items-center justify-between mb-0">
                                    <p class="mb-0 text-amber-500">{{ $post->created_at->diffForHumans() }}</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border {{ $post->client === 'retro' ? 'border-blue-500/30 text-blue-400/90 bg-blue-500/10' : 'border-amber-500/30 text-amber-400/90 bg-amber-500/10' }}">
                                        <div class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $post->client === 'retro' ? 'bg-blue-400' : 'bg-amber-400' }}"></div>
                                        {{ $post->client_label }}
                                    </span>
                                </div>
                                <h3 style="font-size: 1.5em" class="my-2 half-border font-normal text-gray-100">{{ $post->title }}</h3>
                                <p class="text-gray-300 mt-4 text-sm leading-relaxed">{{ Str::limit(strip_tags($post->article_content), 120, '...') }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
