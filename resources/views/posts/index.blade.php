<x-app-layout>
    @section('title', 'Updates & Patch Notes | XileRO')
    @section('description', 'Stay up to date with the latest XileRO and XileRetro patch notes, updates, and announcements. See what\'s new in your favorite Ragnarok Online private server.')
    @section('keywords', 'XileRO updates, patch notes, changelog, Ragnarok Online updates, XileRetro news, server updates')

    @section('structured_data')
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "CollectionPage",
        "name": "XileRO Updates & Patch Notes",
        "description": "Latest updates and patch notes for XileRO and XileRetro Ragnarok Online private servers",
        "url": "{{ route('posts.index') }}",
        "isPartOf": {
            "@@type": "WebSite",
            "name": "XileRO",
            "url": "{{ config('app.url') }}"
        }
    }
    </script>
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "{{ url('/') }}"
            },
            {
                "@@type": "ListItem",
                "position": 2,
                "name": "Updates",
                "item": "{{ route('posts.index') }}"
            }
        ]
    }
    </script>
    @endsection

    <div class="bg-clash-bg min-h-screen pt-28 pb-16 px-4">
        <div class="max-w-6xl w-full mx-auto">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between mb-8">
                <div>
                    <nav class="flex items-center text-sm text-gray-400 mb-4">
                        <a href="/" class="hover:text-amber-500 transition-colors">Home</a>
                        <span class="mx-2 text-gray-600">/</span>
                        <span class="text-amber-500">Updates</span>
                    </nav>
                    <h1 class="text-3xl font-bold text-white mb-2">Updates & Patch Notes</h1>
                    <p class="text-gray-400">Stay up to date with the latest changes and improvements.</p>
                </div>
            </div>

            {{-- Filter Pills --}}
            <div class="mb-8 block-home bg-gray-900 rounded-lg p-5">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-gray-400 text-sm font-medium mr-2">Filter by:</span>
                    <a href="{{ route('posts.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !$selectedClient ? 'bg-amber-500 text-gray-900' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                        All Updates
                    </a>
                    <a href="{{ route('posts.index', ['client' => 'retro']) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $selectedClient === 'retro' ? 'bg-blue-500 text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                        <span class="inline-flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $selectedClient === 'retro' ? 'bg-white' : 'bg-blue-400' }}"></span>
                            XileRetro
                        </span>
                    </a>
                    <a href="{{ route('posts.index', ['client' => 'xilero']) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $selectedClient === 'xilero' ? 'bg-amber-500 text-gray-900' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                        <span class="inline-flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $selectedClient === 'xilero' ? 'bg-gray-900' : 'bg-amber-400' }}"></span>
                            XileRO
                        </span>
                    </a>
                </div>
            </div>

            {{-- Results Count --}}
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-gray-500">
                    Showing <span class="text-white font-medium">{{ $posts->count() }}</span>
                    of <span class="text-white font-medium">{{ $posts->total() }}</span>
                    {{ Str::plural('update', $posts->total()) }}
                </p>
            </div>

            {{-- Posts Grid --}}
            @if($posts->isNotEmpty())
                <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($posts as $post)
                        <a href="{{ route('posts.show', $post) }}"
                           class="group block-home bg-gray-900 rounded-lg overflow-hidden hover:bg-gray-800/80 transition-all duration-200 hover:ring-1 hover:ring-amber-500/30">
                            {{-- Card Content --}}
                            <div class="p-6">
                                {{-- Header with Date & Badge --}}
                                <div class="flex items-center justify-between mb-4">
                                    <time datetime="{{ $post->created_at->toIso8601String() }}"
                                          class="text-sm text-gray-400">
                                        {{ $post->created_at->format('M j, Y') }}
                                    </time>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $post->client === 'retro' ? 'border-blue-500/30 text-blue-400 bg-blue-500/10' : 'border-amber-500/30 text-amber-400 bg-amber-500/10' }}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $post->client === 'retro' ? 'bg-blue-400' : 'bg-amber-400' }}"></span>
                                        {{ $post->client_label }}
                                    </span>
                                </div>

                                {{-- Title --}}
                                <h2 class="text-xl font-semibold text-white mb-3 group-hover:text-amber-400 transition-colors line-clamp-2">
                                    {{ $post->title }}
                                </h2>

                                {{-- Excerpt --}}
                                @if($post->patcher_notice)
                                    <p class="text-gray-400 text-sm leading-relaxed line-clamp-3 mb-4">
                                        {{ Str::limit($post->patcher_notice, 150) }}
                                    </p>
                                @else
                                    <p class="text-gray-400 text-sm leading-relaxed line-clamp-3 mb-4">
                                        {{ Str::limit(strip_tags($post->article_content), 150) }}
                                    </p>
                                @endif

                                {{-- Footer --}}
                                <div class="flex items-center justify-between pt-4 border-t border-gray-800">
                                    <span class="text-xs text-gray-500">
                                        {{ $post->created_at->diffForHumans() }}
                                    </span>
                                    <span class="inline-flex items-center text-amber-500 text-sm font-medium group-hover:text-amber-400 transition-colors">
                                        Read more
                                        <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($posts->hasPages())
                    <div class="mt-10">
                        {{ $posts->withQueryString()->links() }}
                    </div>
                @endif
            @else
                {{-- Empty State --}}
                <div class="block-home bg-gray-900 rounded-lg p-12 text-center">
                    <div class="w-16 h-16 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-300 mb-2">No updates found</h3>
                    <p class="text-gray-500 mb-4">
                        @if($selectedClient)
                            There are no updates for {{ $selectedClient === 'retro' ? 'XileRetro' : 'XileRO' }} yet.
                        @else
                            There are no updates posted yet. Check back soon!
                        @endif
                    </p>
                    @if($selectedClient)
                        <a href="{{ route('posts.index') }}"
                           class="inline-flex items-center gap-2 text-amber-400 hover:text-amber-300 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            View all updates
                        </a>
                    @endif
                </div>
            @endif

            {{-- Quick Links Section --}}
            <div class="mt-16 pt-8 border-t border-gray-800">
                <h3 class="text-lg font-semibold text-white mb-6">Quick Links</h3>
                <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                    <a href="/" class="block-home bg-gray-900 rounded-lg p-4 hover:bg-gray-800/80 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-600 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                            </div>
                            <div>
                                <span class="font-medium text-white group-hover:text-amber-400 transition-colors">Home</span>
                                <p class="text-xs text-gray-500">Back to homepage</p>
                            </div>
                        </div>
                    </a>
                    <a href="https://wiki.xilero.net" target="_blank" rel="noopener" class="block-home bg-gray-900 rounded-lg p-4 hover:bg-gray-800/80 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                                </svg>
                            </div>
                            <div>
                                <span class="font-medium text-white group-hover:text-amber-400 transition-colors">Wiki</span>
                                <p class="text-xs text-gray-500">Game guides & info</p>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('donate-shop') }}" class="block-home bg-gray-900 rounded-lg p-4 hover:bg-gray-800/80 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <span class="font-medium text-white group-hover:text-amber-400 transition-colors">Uber Shop</span>
                                <p class="text-xs text-gray-500">Premium items</p>
                            </div>
                        </div>
                    </a>
                    <a href="https://discord.gg/hp7CS6k" target="_blank" rel="noopener" class="block-home bg-gray-900 rounded-lg p-4 hover:bg-gray-800/80 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M16.942 4.967a13.533 13.533 0 00-3.332-1.033.05.05 0 00-.053.025 9.441 9.441 0 00-.414.853 12.476 12.476 0 00-3.744 0 9.111 9.111 0 00-.421-.853.052.052 0 00-.053-.025 13.499 13.499 0 00-3.331 1.033.047.047 0 00-.022.018C1.47 8.252.828 11.455 1.15 14.611a.056.056 0 00.021.038 13.581 13.581 0 004.089 2.066.052.052 0 00.057-.019 9.63 9.63 0 00.836-1.359.051.051 0 00-.028-.072 8.943 8.943 0 01-1.277-.608.052.052 0 01-.006-.087c.086-.065.171-.133.253-.201a.05.05 0 01.052-.007c2.679 1.223 5.578 1.223 8.23 0a.05.05 0 01.053.006c.082.068.167.137.254.202a.052.052 0 01-.005.087c-.408.238-.834.44-1.278.607a.051.051 0 00-.028.073c.235.464.505.904.836 1.359a.052.052 0 00.057.019 13.546 13.546 0 004.094-2.066.052.052 0 00.021-.037c.378-3.648-.63-6.815-2.666-9.626a.041.041 0 00-.021-.019z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="font-medium text-white group-hover:text-amber-400 transition-colors">Discord</span>
                                <p class="text-xs text-gray-500">Join our community</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
