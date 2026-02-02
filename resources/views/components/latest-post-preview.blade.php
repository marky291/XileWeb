<section id="latest-updates" class="relative overflow-hidden py-12 bg-clash-bg md:px-24">
    <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
        {{-- Header --}}
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl font-bold text-gray-100 mb-2">Latest Updates</h2>
                <p class="text-gray-400">News, patches, and announcements from the team.</p>
            </div>
            <a href="{{ route('posts.index') }}" class="hidden md:inline-flex items-center gap-2 text-amber-500 hover:text-amber-400 font-medium transition-colors">
                View all updates
                <i class="fas fa-arrow-right text-sm"></i>
            </a>
        </div>

        {{-- Posts Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach(\App\Models\Post::orderBy('created_at', 'desc')->take(3)->get() as $index => $post)
                <a href="{{ route('posts.show', $post) }}" class="group card-glow-wrapper transition-all duration-300 hover:-translate-y-1 no-underline">
                    <div class="card-glow-inner">
                        {{-- Image --}}
                        <div class="relative h-48 overflow-hidden bg-gray-800">
                            @if($post->image)
                                <img
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    src="{{ Storage::disk('public')->url($post->image) }}"
                                    alt="{{ $post->title }}"
                                >
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-transparent to-transparent"></div>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900">
                                    <i class="fas fa-scroll text-4xl text-gray-700"></i>
                                </div>
                            @endif

                            {{-- Badge --}}
                            <div class="absolute top-4 right-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium backdrop-blur-sm {{ $post->client === 'retro' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $post->client === 'retro' ? 'bg-blue-400' : 'bg-amber-400' }}"></span>
                                    {{ $post->client_label }}
                                </span>
                            </div>

                            {{-- Date on image --}}
                            <div class="absolute bottom-4 left-4">
                                <span class="text-sm text-gray-300">{{ $post->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="p-5">
                            <h3 class="text-lg font-semibold text-gray-100 mb-2 group-hover:text-amber-400 transition-colors line-clamp-2">
                                {{ $post->title }}
                            </h3>
                            <p class="text-gray-400 text-sm leading-relaxed line-clamp-2">
                                {{ Str::limit(strip_tags($post->article_content), 100, '...') }}
                            </p>

                            {{-- Read more --}}
                            <div class="mt-4 flex items-center text-amber-500 text-sm font-medium">
                                <span class="group-hover:underline">Read more</span>
                                <i class="fas fa-chevron-right ml-2 text-xs transition-transform group-hover:translate-x-1"></i>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Mobile View All --}}
        <div class="mt-8 text-center md:hidden">
            <a href="{{ route('posts.index') }}" class="inline-flex items-center gap-2 text-amber-500 hover:text-amber-400 font-medium transition-colors">
                View all updates
                <i class="fas fa-arrow-right text-sm"></i>
            </a>
        </div>
    </div>
</section>
