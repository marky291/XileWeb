<x-app-layout>
    @section("title", $post->title . " | XileRO Updates")
    @section('description', $post->patcher_notice ?: 'Latest updates and changes to XileRO - ' . $post->title)
    @section('keywords', 'XileRO, Updates, Changelog, Patch Notes, ' . $post->title)
    @section('og_type', 'article')
    @section('canonical', route('posts.show', $post))
    @if($post->image)
        @section('og_image', Storage::disk('public')->url($post->image))
    @endif

    @section('structured_data')
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Article",
        "headline": "{{ $post->title }}",
        "description": "{{ $post->patcher_notice ?: 'Latest updates and changes to XileRO' }}",
        "datePublished": "{{ $post->created_at->toIso8601String() }}",
        "dateModified": "{{ $post->updated_at->toIso8601String() }}",
        "author": {
            "@@type": "Organization",
            "name": "XileRO"
        },
        "publisher": {
            "@@type": "Organization",
            "name": "XileRO",
            "logo": {
                "@@type": "ImageObject",
                "url": "{{ asset('images/logo.png') }}"
            }
        },
        @if($post->image)
        "image": "{{ Storage::disk('public')->url($post->image) }}",
        @endif
        "mainEntityOfPage": {
            "@@type": "WebPage",
            "@@id": "{{ url()->current() }}"
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
            },
            {
                "@@type": "ListItem",
                "position": 3,
                "name": "{{ $post->title }}",
                "item": "{{ url()->current() }}"
            }
        ]
    }
    </script>
    @endsection

    <div class="bg-clash-bg min-h-screen pt-28 pb-16 px-4">
        <div class="max-w-7xl w-full mx-auto">
            {{-- Breadcrumb --}}
            <nav class="flex items-center text-sm text-gray-400 mb-6">
                <a href="/" class="hover:text-amber-500 transition-colors">Home</a>
                <span class="mx-2 text-gray-600">/</span>
                <a href="{{ route('posts.index') }}" class="hover:text-amber-500 transition-colors">Updates</a>
                <span class="mx-2 text-gray-600">/</span>
                <span class="text-gray-500 truncate max-w-xs">{{ $post->title }}</span>
            </nav>

            <div class="flex flex-col lg:flex-row gap-8">
                {{-- Sidebar --}}
                <aside class="lg:w-80 shrink-0 order-2 lg:order-1">
                    <div class="lg:sticky lg:top-24 space-y-6">
                        {{-- Table of Contents --}}
                        <div class="block-home bg-gray-900 rounded-lg p-5" x-data="tableOfContents()" x-init="init()">
                            <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">
                                <i class="fas fa-list-ul text-amber-500 mr-2"></i>Contents
                            </h3>
                            <nav id="toc" class="space-y-1 text-sm max-h-64 overflow-y-auto">
                                <template x-if="headings.length === 0">
                                    <p class="text-gray-500 text-sm italic">No sections found</p>
                                </template>
                                <template x-for="(heading, index) in headings" :key="index">
                                    <a :href="'#' + heading.id"
                                       :class="{'pl-4': heading.level === 3, 'pl-8': heading.level === 4}"
                                       class="block py-1.5 text-gray-400 hover:text-amber-400 transition-colors truncate"
                                       x-text="heading.text">
                                    </a>
                                </template>
                            </nav>
                        </div>

                        {{-- Post Stats --}}
                        <div class="block-home bg-gray-900 rounded-lg p-5">
                            <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">
                                <i class="fas fa-chart-bar text-amber-500 mr-2"></i>Stats
                            </h3>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-800/50 rounded-lg p-3 text-center">
                                    <p class="text-2xl font-bold text-white">{{ number_format($post->views) }}</p>
                                    <p class="text-xs text-gray-500">Views</p>
                                </div>
                                <div class="bg-gray-800/50 rounded-lg p-3 text-center">
                                    <p class="text-2xl font-bold text-white">{{ ceil(str_word_count(strip_tags($post->article_content)) / 200) }}</p>
                                    <p class="text-xs text-gray-500">Min Read</p>
                                </div>
                            </div>
                        </div>

                        {{-- Related Posts --}}
                        @if($relatedPosts->isNotEmpty())
                            <div class="block-home bg-gray-900 rounded-lg p-5">
                                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">
                                    <i class="fas fa-newspaper text-amber-500 mr-2"></i>More Updates
                                </h3>
                                <div class="space-y-3">
                                    @foreach($relatedPosts as $relatedPost)
                                        <a href="{{ route('posts.show', $relatedPost) }}" class="block group">
                                            <div class="flex items-start gap-3">
                                                <div class="w-2 h-2 rounded-full mt-2 shrink-0 {{ $relatedPost->client === 'retro' ? 'bg-blue-400' : 'bg-amber-400' }}"></div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-gray-300 group-hover:text-amber-400 transition-colors line-clamp-2">{{ $relatedPost->title }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">{{ $relatedPost->created_at->format('M j, Y') }} &middot; {{ number_format($relatedPost->views) }} views</p>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                                <a href="{{ route('posts.index') }}" class="inline-flex items-center text-amber-500 hover:text-amber-400 text-sm font-medium mt-4 transition-colors">
                                    View all updates <i class="fas fa-arrow-right ml-2 text-xs"></i>
                                </a>
                            </div>
                        @endif

                        {{-- Quick Links --}}
                        <div class="block-home bg-gray-900 rounded-lg p-5">
                            <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">
                                <i class="fas fa-link text-amber-500 mr-2"></i>Quick Links
                            </h3>
                            <div class="space-y-2">
                                <a href="{{ route('posts.index') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 transition-colors group">
                                    <div class="w-8 h-8 bg-amber-600 rounded-lg flex items-center justify-center shrink-0">
                                        <i class="fas fa-list text-white text-xs"></i>
                                    </div>
                                    <span class="text-sm text-gray-300 group-hover:text-white transition-colors">All Updates</span>
                                </a>
                                <a href="https://wiki.xilero.net" target="_blank" rel="noopener" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 transition-colors group">
                                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shrink-0">
                                        <i class="fas fa-book text-white text-xs"></i>
                                    </div>
                                    <span class="text-sm text-gray-300 group-hover:text-white transition-colors">Wiki</span>
                                </a>
                                <a href="{{ route('donate-shop') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 transition-colors group">
                                    <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center shrink-0">
                                        <i class="fas fa-store text-white text-xs"></i>
                                    </div>
                                    <span class="text-sm text-gray-300 group-hover:text-white transition-colors">Uber Shop</span>
                                </a>
                                <a href="{{ route('item-database') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-800 transition-colors group">
                                    <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center shrink-0">
                                        <i class="fas fa-database text-white text-xs"></i>
                                    </div>
                                    <span class="text-sm text-gray-300 group-hover:text-white transition-colors">Item Database</span>
                                </a>
                            </div>
                        </div>

                        {{-- Discord CTA --}}
                        <div class="block-home bg-gradient-to-br from-indigo-900/50 to-gray-900 rounded-lg p-5 border border-indigo-500/20">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                                    <i class="fab fa-discord text-white text-xl"></i>
                                </div>
                                <h3 class="font-semibold text-white mb-1">Join our Discord</h3>
                                <p class="text-sm text-gray-400 mb-4">Get help, chat with players, and stay updated!</p>
                                <a href="https://discord.gg/hp7CS6k" target="_blank" rel="noopener" class="inline-flex items-center justify-center w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition-colors">
                                    <i class="fab fa-discord mr-2"></i>Join Server
                                </a>
                            </div>
                        </div>
                    </div>
                </aside>

                {{-- Main Content --}}
                <div class="flex-1 min-w-0 order-1 lg:order-2">
                    {{-- Header --}}
                    <div class="mb-6">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $post->client === 'retro' ? 'border-blue-500/30 text-blue-400 bg-blue-500/10' : 'border-amber-500/30 text-amber-400 bg-amber-500/10' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $post->client === 'retro' ? 'bg-blue-400' : 'bg-amber-400' }}"></span>
                                {{ $post->client_label }}
                            </span>
                            <span class="text-gray-500 text-sm">{{ $post->created_at->format('M j, Y') }}</span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-3">{{ $post->title }}</h1>
                        @if($post->patcher_notice)
                            <p class="text-lg text-gray-400">{{ $post->patcher_notice }}</p>
                        @endif
                    </div>

                    {{-- Main Content Card --}}
                    <div class="block-home bg-gray-900 rounded-lg overflow-hidden">
                        {{-- Featured Image --}}
                        @if($post->image)
                            <div class="aspect-video overflow-hidden">
                                <img src="{{ Storage::disk('public')->url($post->image) }}"
                                     alt="{{ $post->title }}"
                                     class="w-full h-full object-cover">
                            </div>
                        @endif

                        {{-- Article Content --}}
                        <article class="p-6 md:p-8" x-data>
                            <div id="article-content" class="prose prose-invert prose-lg max-w-none
                                prose-headings:text-white prose-headings:font-bold prose-headings:scroll-mt-24
                                prose-h2:text-2xl prose-h2:mt-8 prose-h2:mb-4 prose-h2:pb-2 prose-h2:border-b prose-h2:border-gray-800
                                prose-h3:text-xl prose-h3:mt-6 prose-h3:mb-3
                                prose-h4:text-lg prose-h4:mt-4 prose-h4:mb-2
                                prose-p:text-gray-300 prose-p:leading-relaxed
                                prose-a:text-amber-400 prose-a:no-underline hover:prose-a:text-amber-300
                                prose-strong:text-white
                                prose-ul:text-gray-300 prose-ol:text-gray-300
                                prose-li:marker:text-amber-500
                                prose-code:text-amber-300 prose-code:bg-gray-800 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-code:text-sm
                                prose-pre:bg-gray-800 prose-pre:border prose-pre:border-gray-700
                                prose-blockquote:border-l-amber-500 prose-blockquote:bg-gray-800/50 prose-blockquote:text-gray-300 prose-blockquote:not-italic
                                prose-table:border-collapse
                                prose-th:bg-gray-800 prose-th:text-white prose-th:font-semibold prose-th:px-4 prose-th:py-3 prose-th:text-left prose-th:border prose-th:border-gray-700
                                prose-td:px-4 prose-td:py-3 prose-td:border prose-td:border-gray-700 prose-td:text-gray-300
                                prose-hr:border-gray-800
                                prose-img:rounded-lg">
                                <x-markdown>
                                    {{ $post->article_content }}
                                </x-markdown>
                            </div>
                        </article>

                        {{-- Footer --}}
                        <footer class="px-6 md:px-8 py-5 border-t border-gray-800 bg-gray-800/30">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex items-center gap-4 text-sm text-gray-400">
                                    <span class="flex items-center gap-2">
                                        <i class="far fa-calendar text-gray-500"></i>
                                        {{ $post->created_at->format('F j, Y') }}
                                    </span>
                                    <span class="flex items-center gap-2">
                                        <i class="far fa-clock text-gray-500"></i>
                                        {{ ceil(str_word_count(strip_tags($post->article_content)) / 200) }} min read
                                    </span>
                                    <span class="flex items-center gap-2">
                                        <i class="far fa-eye text-gray-500"></i>
                                        {{ number_format($post->views) }} {{ Str::plural('view', $post->views) }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button
                                        onclick="navigator.clipboard.writeText(window.location.href).then(() => { this.innerHTML = '<i class=\'fas fa-check mr-2\'></i>Copied!'; setTimeout(() => { this.innerHTML = '<i class=\'fas fa-link mr-2\'></i>Copy Link'; }, 2000); })"
                                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 hover:text-white rounded-lg text-sm font-medium transition-colors"
                                    >
                                        <i class="fas fa-link mr-2"></i>Copy Link
                                    </button>
                                </div>
                            </div>
                        </footer>
                    </div>

                    {{-- Previous/Next Navigation --}}
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if($previousPost)
                            <a href="{{ route('posts.show', $previousPost) }}" class="block-home bg-gray-900 rounded-lg p-4 hover:bg-gray-800/80 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center shrink-0 group-hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-arrow-left text-gray-400 group-hover:text-amber-400 transition-colors text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-[10px] text-gray-500 uppercase tracking-wider">Previous</p>
                                        <p class="text-sm font-medium text-white group-hover:text-amber-400 transition-colors truncate">{{ $previousPost->title }}</p>
                                    </div>
                                </div>
                            </a>
                        @else
                            <div></div>
                        @endif

                        @if($nextPost)
                            <a href="{{ route('posts.show', $nextPost) }}" class="block-home bg-gray-900 rounded-lg p-4 hover:bg-gray-800/80 transition-colors group text-right">
                                <div class="flex items-center gap-3 justify-end">
                                    <div class="min-w-0">
                                        <p class="text-[10px] text-gray-500 uppercase tracking-wider">Next</p>
                                        <p class="text-sm font-medium text-white group-hover:text-amber-400 transition-colors truncate">{{ $nextPost->title }}</p>
                                    </div>
                                    <div class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center shrink-0 group-hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-arrow-right text-gray-400 group-hover:text-amber-400 transition-colors text-sm"></i>
                                    </div>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table of Contents Script --}}
    <script>
        function tableOfContents() {
            return {
                headings: [],
                init() {
                    const article = document.getElementById('article-content');
                    if (!article) return;

                    const headingElements = article.querySelectorAll('h2, h3, h4');
                    this.headings = Array.from(headingElements).map((heading, index) => {
                        const id = 'heading-' + index;
                        heading.id = id;
                        return {
                            id: id,
                            text: heading.textContent,
                            level: parseInt(heading.tagName.charAt(1))
                        };
                    });
                }
            }
        }
    </script>
</x-app-layout>
