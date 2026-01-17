<x-app-layout>
    @section("title", $post->title . " | XileRO Patch Notes & Updates")
    @section('description', $post->patcher_notice ?: 'Latest updates and changes to XileRO - ' . $post->title)
    @section('keywords', 'XileRO, Updates, Changelog, Patch Notes, ' . $post->title)
    @section('og_type', 'article')
    @section('canonical', route('posts.show', $post))

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
        "mainEntityOfPage": {
            "@@type": "WebPage",
            "@@id": "{{ url()->current() }}"
        },
        "wordCount": {{ str_word_count(strip_tags($post->article_content)) }}
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
                "item": "{{ url('/posts') }}"
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

    {{-- Progress Bar --}}
    <div id="reading-progress" class="fixed top-0 left-0 w-0 h-1 bg-amber-500 z-50"></div>

    {{-- Flat Header --}}
    <section class="bg-gray-950 pt-20 lg:pt-24 py-12 md:py-16">
        <div class="max-w-screen-xl w-full mx-auto px-5">
            {{-- Simple Breadcrumbs --}}
            <nav class="flex items-center text-sm text-gray-300 mb-8 bg-gray-900 border border-gray-800 rounded px-4 py-3">
                <a href="/" class="hover:text-amber-500 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Home
                </a>
                <span class="mx-3 text-gray-500">/</span>
                <a href="/posts" class="hover:text-amber-500 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path>
                    </svg>
                    Updates
                </a>
                <span class="mx-3 text-gray-500">/</span>
                <span class="text-amber-500 font-semibold">{{ $post->title }}</span>
            </nav>
            
            {{-- Page Title --}}
            <div class="flex flex-col lg:flex-row items-start justify-between gap-8">
                <div class="flex-1">
                    <div class="flex items-center gap-4 mb-4 mt-4">
                        <div class="w-12 h-12 bg-amber-600 rounded flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl lg:text-4xl font-bold text-gray-100 leading-tight">{{ $post->title }}</h1>
                            <p class="text-lg text-gray-300">{{ $post->patcher_notice ?: 'Latest updates and changes to XileRO' }}</p>
                        </div>
                    </div>
                    
                    {{-- Article Meta --}}
                    <div class="flex flex-wrap items-center gap-6 text-sm text-gray-400">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Published {{ $post->created_at->format('M j, Y') }}</span>
                        </div>
                        <div class="flex items-center" id="read-time">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                            <span>2 min read</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <span class="px-3 py-1 border border-amber-600 text-amber-400 rounded text-xs font-medium">Update</span>
                            <span class="px-3 py-1 border border-gray-600 text-gray-300 rounded text-xs font-medium">{{ $post->client_label }}</span>
                        </div>
                    </div>
                </div>
                
                {{-- Simple Action Buttons --}}
                <div class="flex items-center gap-3 flex-shrink-0">
                    <button onclick="window.print()" class="flex items-center px-4 py-2.5 bg-gray-800 hover:bg-gray-700 border border-gray-700 text-gray-300 hover:text-white rounded transition-colors duration-200" title="Print Article">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span class="hidden sm:inline">Print</span>
                    </button>
                    <button onclick="shareArticle()" class="flex items-center px-4 py-2.5 bg-gray-800 hover:bg-gray-700 border border-gray-700 text-gray-300 hover:text-white rounded transition-colors duration-200" title="Share Article">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        <span class="hidden sm:inline">Share</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="bg-gray-950 pb-12">
        <div class="max-w-screen-xl w-full mx-auto px-5">
            <div class="grid grid-cols-12 gap-8">
                {{-- Sidebar --}}
                <aside class="col-span-12 lg:col-span-3">
                    <div class="space-y-6 lg:sticky lg:top-8">
                        {{-- Table of Contents --}}
                        <div class="bg-gray-900 border border-gray-800 rounded p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-100">Contents</h3>
                                <button id="toc-collapse" class="lg:hidden text-gray-400 hover:text-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </div>
                            <nav id="toc" class="space-y-1">
                                {{-- Generated by JavaScript --}}
                            </nav>
                        </div>

                        {{-- Quick Navigation --}}
                        <div class="bg-gray-900 border border-gray-800 rounded p-6">
                            <h3 class="text-lg font-bold text-gray-100 mb-4">Quick Navigation</h3>
                            <div class="space-y-2">
                                <a href="/posts" class="flex items-center px-3 py-2.5 text-gray-300 hover:text-amber-500 hover:bg-gray-800 rounded transition-colors duration-200">
                                    <div class="w-6 h-6 bg-amber-600 rounded flex items-center justify-center mr-3">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"></path>
                                        </svg>
                                    </div>
                                    <span class="font-medium">All Updates</span>
                                </a>
                                <a href="/wiki" class="flex items-center px-3 py-2.5 text-gray-300 hover:text-amber-500 hover:bg-gray-800 rounded transition-colors duration-200">
                                    <div class="w-6 h-6 bg-blue-600 rounded flex items-center justify-center mr-3">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                                        </svg>
                                    </div>
                                    <span class="font-medium">Wiki</span>
                                </a>
                                <a href="/download" class="flex items-center px-3 py-2.5 text-gray-300 hover:text-amber-500 hover:bg-gray-800 rounded transition-colors duration-200">
                                    <div class="w-6 h-6 bg-green-600 rounded flex items-center justify-center mr-3">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                    </div>
                                    <span class="font-medium">Download</span>
                                </a>
                                <a href="/donate" class="flex items-center px-3 py-2.5 text-gray-300 hover:text-amber-500 hover:bg-gray-800 rounded transition-colors duration-200">
                                    <div class="w-6 h-6 bg-purple-600 rounded flex items-center justify-center mr-3">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </div>
                                    <span class="font-medium">Donate</span>
                                </a>
                            </div>
                        </div>

                        {{-- Community Section --}}
                        <div class="bg-gray-900 border border-gray-800 rounded p-6 text-center">
                            <div class="w-12 h-12 bg-indigo-600 rounded flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M16.942 4.967a13.533 13.533 0 00-3.332-1.033.05.05 0 00-.053.025 9.441 9.441 0 00-.414.853 12.476 12.476 0 00-3.744 0 9.111 9.111 0 00-.421-.853.052.052 0 00-.053-.025 13.499 13.499 0 00-3.331 1.033.047.047 0 00-.022.018C1.47 8.252.828 11.455 1.15 14.611a.056.056 0 00.021.038 13.581 13.581 0 004.089 2.066.052.052 0 00.057-.019 9.63 9.63 0 00.836-1.359.051.051 0 00-.028-.072 8.943 8.943 0 01-1.277-.608.052.052 0 01-.006-.087c.086-.065.171-.133.253-.201a.05.05 0 01.052-.007c2.679 1.223 5.578 1.223 8.23 0a.05.05 0 01.053.006c.082.068.167.137.254.202a.052.052 0 01-.005.087c-.408.238-.834.44-1.278.607a.051.051 0 00-.028.073c.235.464.505.904.836 1.359a.052.052 0 00.057.019 13.546 13.546 0 004.094-2.066.052.052 0 00.021-.037c.378-3.648-.63-6.815-2.666-9.626a.041.041 0 00-.021-.019z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-100 mb-2">Need Help?</h3>
                            <p class="text-gray-300 mb-4 text-sm leading-relaxed">Join our Discord community for instant support and discussions!</p>
                            <a href="https://discord.gg/hp7CS6k" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-medium text-sm transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M16.942 4.967a13.533 13.533 0 00-3.332-1.033.05.05 0 00-.053.025 9.441 9.441 0 00-.414.853 12.476 12.476 0 00-3.744 0 9.111 9.111 0 00-.421-.853.052.052 0 00-.053-.025 13.499 13.499 0 00-3.331 1.033.047.047 0 00-.022.018C1.47 8.252.828 11.455 1.15 14.611a.056.056 0 00.021.038 13.581 13.581 0 004.089 2.066.052.052 0 00.057-.019 9.63 9.63 0 00.836-1.359.051.051 0 00-.028-.072 8.943 8.943 0 01-1.277-.608.052.052 0 01-.006-.087c.086-.065.171-.133.253-.201a.05.05 0 01.052-.007c2.679 1.223 5.578 1.223 8.23 0a.05.05 0 01.053.006c.082.068.167.137.254.202a.052.052 0 01-.005.087c-.408.238-.834.44-1.278.607a.051.051 0 00-.028.073c.235.464.505.904.836 1.359a.052.052 0 00.057.019 13.546 13.546 0 004.094-2.066.052.052 0 00.021-.037c.378-3.648-.63-6.815-2.666-9.626a.041.041 0 00-.021-.019z"/>
                                </svg>
                                Join Discord
                            </a>
                        </div>
                    </div>
                </aside>

                {{-- Main Content --}}
                <main class="col-span-12 lg:col-span-9">
                    <div class="bg-gray-900 border border-gray-800 rounded p-6 lg:p-8">
                        <article class="prose prose-lg max-w-none post-content">
                            <x-markdown class="markdown">
                                {{ $post->article_content }}
                            </x-markdown>
                        </article>

                        {{-- Article Rating --}}
                        <div class="mt-12 pt-8 border-t border-gray-700">
                            <div class="bg-gray-800 border border-gray-700 rounded p-6">
                                <h4 class="text-lg font-semibold text-gray-100 mb-4">Was this update helpful?</h4>
                                <div class="flex items-center gap-4">
                                    <button onclick="rateArticle(true)" class="flex items-center px-4 py-2 border border-green-600 text-green-400 hover:bg-green-600 hover:text-white rounded transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"></path>
                                        </svg>
                                        <span>Yes</span>
                                    </button>
                                    <button onclick="rateArticle(false)" class="flex items-center px-4 py-2 border border-red-600 text-red-400 hover:bg-red-600 hover:text-white rounded transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018c.163 0 .326.02.485.06L17 4m-7 10v2a2 2 0 002 2h.095c.5 0 .905-.405.905-.905 0-.714.211-1.412.608-2.006L17 13V4m-7 10h2m5-10h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5"></path>
                                        </svg>
                                        <span>No</span>
                                    </button>
                                    <div class="ml-4 text-sm text-gray-400">
                                        <span id="feedback-message" class="hidden">Thanks for your feedback!</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <footer class="mt-8 pt-6 border-t border-gray-700">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <div class="flex items-center text-sm text-gray-400 mb-2">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Published {{ $post->created_at->format('F j, Y') }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span>{{ str_word_count(strip_tags($post->article_content)) }} words</span>
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <a href="https://discord.gg/hp7CS6k" class="inline-flex items-center text-amber-500 hover:text-amber-400 font-semibold transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M16.942 4.967a13.533 13.533 0 00-3.332-1.033.05.05 0 00-.053.025 9.441 9.441 0 00-.414.853 12.476 12.476 0 00-3.744 0 9.111 9.111 0 00-.421-.853.052.052 0 00-.053-.025 13.499 13.499 0 00-3.331 1.033.047.047 0 00-.022.018C1.47 8.252.828 11.455 1.15 14.611a.056.056 0 00.021.038 13.581 13.581 0 004.089 2.066.052.052 0 00.057-.019 9.63 9.63 0 00.836-1.359.051.051 0 00-.028-.072 8.943 8.943 0 01-1.277-.608.052.052 0 01-.006-.087c.086-.065.171-.133.253-.201a.05.05 0 01.052-.007c2.679 1.223 5.578 1.223 8.23 0a.05.05 0 01.053.006c.082.068.167.137.254.202a.052.052 0 01-.005.087c-.408.238-.834.44-1.278.607a.051.051 0 00-.028.073c.235.464.505.904.836 1.359a.052.052 0 00.057.019 13.546 13.546 0 004.094-2.066.052.052 0 00.021-.037c.378-3.648-.63-6.815-2.666-9.626a.041.041 0 00-.021-.019z"/>
                                        </svg>
                                        Need help? Ask on Discord
                                    </a>
                                </div>
                            </div>
                        </footer>
                    </div>
                </main>
            </div>
        </div>
    </section>

    {{-- Simple Action Buttons --}}
    <div class="fixed bottom-6 right-6 flex flex-col gap-3 z-40">
        <button id="back-to-top" class="hidden w-12 h-12 bg-amber-600 hover:bg-amber-700 border border-amber-500 text-white rounded transition-colors duration-200 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
            </svg>
        </button>
        
        <button id="toggle-sidebar" class="lg:hidden w-12 h-12 bg-gray-800 hover:bg-gray-700 border border-gray-700 text-white rounded transition-colors duration-200 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    {{-- Flat Styling --}}
    <style>
        /* Flat post content */
        .post-content {
            @apply text-gray-300 leading-7;
            font-size: 16px;
            line-height: 1.75;
        }

        /* Clean headings */
        .post-content h1 {
            @apply text-3xl font-bold text-gray-100 mt-0 mb-8 pb-4 border-b-2 border-gray-700;
            color: rgb(243 244 246) !important; /* gray-100 */
            font-size: 1.875rem !important; /* text-3xl */
            font-weight: 700 !important; /* font-bold */
            margin-top: 0 !important;
            margin-bottom: 2rem !important;
        }

        .post-content h2 {
            @apply text-2xl font-bold text-gray-100 mt-10 mb-6 first:mt-0 pb-2 border-b border-gray-700;
            color: rgb(243 244 246) !important; /* gray-100 */
            font-size: 1.5rem !important; /* text-2xl */
            font-weight: 700 !important; /* font-bold */
            margin-top: 2.5rem !important;
            margin-bottom: 1.5rem !important;
        }

        .post-content h3 {
            @apply text-xl font-bold text-gray-100 mt-8 mb-5;
            color: rgb(243 244 246) !important; /* gray-100 */
            font-size: 1.25rem !important; /* text-xl */
            font-weight: 700 !important; /* font-bold */
            margin-top: 2rem !important;
            margin-bottom: 1.25rem !important;
        }

        .post-content h4 {
            @apply text-lg font-semibold text-gray-100 mt-6 mb-4;
            color: rgb(243 244 246) !important; /* gray-100 */
            font-size: 1.125rem !important; /* text-lg */
            font-weight: 600 !important; /* font-semibold */
            margin-top: 1.5rem !important;
            margin-bottom: 1rem !important;
        }

        /* Paragraph styling */
        .post-content p {
            @apply mb-6 text-gray-300 text-base leading-7;
        }

        /* Lists */
        .post-content ul,
        .post-content ol {
            @apply mb-6 text-gray-300;
        }

        .post-content ul {
            @apply list-disc pl-6;
        }

        .post-content ol {
            @apply list-decimal pl-6;
        }

        .post-content li {
            @apply mb-2 leading-7;
        }

        /* Links */
        .post-content a {
            @apply text-amber-400 hover:text-amber-300 focus:text-amber-300 visited:text-amber-500 active:text-amber-200 underline font-semibold transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-opacity-50 rounded-sm;
            color: rgb(251 191 36) !important; /* amber-400 */
        }

        .post-content a:hover {
            color: rgb(252 211 77) !important; /* amber-300 */
        }

        .post-content a:focus {
            color: rgb(252 211 77) !important; /* amber-300 */
        }

        .post-content a:visited {
            color: rgb(245 158 11) !important; /* amber-500 */
        }

        .post-content a:active {
            color: rgb(254 240 138) !important; /* amber-200 */
        }

        /* Code styling */
        .post-content code {
            @apply bg-gray-800 text-gray-100 px-2 py-1 rounded font-mono text-sm border border-gray-700;
        }

        .post-content pre {
            @apply bg-gray-800 border border-gray-700 rounded p-6 overflow-x-auto mb-6;
        }

        .post-content pre code {
            @apply bg-transparent p-0 border-0 text-gray-100;
        }

        /* Flat tables */
        .post-content table {
            @apply w-full border-collapse mb-8 bg-gray-800 border border-gray-700 rounded;
        }

        .post-content table th {
            @apply bg-gray-700 text-gray-100 font-bold text-left p-4 border-b border-gray-600;
        }

        .post-content table td {
            @apply text-gray-300 p-4 border-b border-gray-700;
        }

        .post-content table tr:last-child td {
            @apply border-b-0;
        }

        .post-content table tr:hover {
            @apply bg-gray-700;
        }

        /* Blockquotes */
        .post-content blockquote {
            @apply border-l-4 border-amber-500 pl-6 py-4 bg-gray-800 text-gray-300 my-6 italic;
        }

        /* Dividers */
        .post-content hr {
            @apply border-gray-700 my-8;
        }

        /* Simple TOC */
        .toc-link {
            @apply block py-2 px-3 text-gray-400 hover:text-amber-500 hover:bg-gray-800 rounded transition-colors duration-200 text-sm;
        }

        .toc-link.active {
            @apply text-amber-500 bg-gray-800 border-l-2 border-amber-500;
        }

        /* Progress bar */
        #reading-progress {
            transition: width 0.3s ease;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }
        }
    </style>

    {{-- Simple JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const content = document.querySelector('.post-content');
            const toc = document.getElementById('toc');
            const progressBar = document.getElementById('reading-progress');
            const backToTopBtn = document.getElementById('back-to-top');
            
            // Calculate reading time
            if (content) {
                const wordCount = content.textContent.trim().split(/\s+/).length;
                const readTime = Math.ceil(wordCount / 200);
                const readTimeElement = document.getElementById('read-time');
                if (readTimeElement) {
                    readTimeElement.innerHTML = `
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        <span>${readTime} min read</span>
                    `;
                }
            }
            
            // Reading progress
            function updateProgress() {
                const scrollTop = window.pageYOffset;
                const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
                const progress = (scrollTop / scrollHeight) * 100;
                
                if (progressBar) {
                    progressBar.style.width = progress + '%';
                }
                
                if (backToTopBtn) {
                    if (scrollTop > 500) {
                        backToTopBtn.classList.remove('hidden');
                    } else {
                        backToTopBtn.classList.add('hidden');
                    }
                }
            }
            
            window.addEventListener('scroll', updateProgress);
            
            // Back to top
            if (backToTopBtn) {
                backToTopBtn.addEventListener('click', () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
            
            // Generate TOC
            if (content && toc) {
                const headings = content.querySelectorAll('h2, h3, h4');
                
                if (headings.length === 0) {
                    toc.innerHTML = '<div class="text-gray-500 text-sm text-center py-4 italic">No sections found</div>';
                    return;
                }
                
                let tocHTML = '';
                headings.forEach((heading, index) => {
                    const id = 'section-' + index;
                    heading.id = id;
                    
                    const level = heading.tagName.toLowerCase();
                    const indent = level === 'h3' ? 'ml-4' : level === 'h4' ? 'ml-8' : '';
                    
                    tocHTML += `
                        <a href="#${id}" class="toc-link block py-2 px-3 text-gray-400 hover:text-amber-500 hover:bg-gray-800 rounded transition-colors duration-200 text-sm ${indent}">
                            ${heading.textContent}
                        </a>
                    `;
                });
                
                toc.innerHTML = tocHTML;
                
                // Add click handlers
                document.querySelectorAll('.toc-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const targetId = this.getAttribute('href');
                        const targetElement = document.querySelector(targetId);
                        
                        if (targetElement) {
                            targetElement.scrollIntoView({ behavior: 'smooth' });
                            
                            document.querySelectorAll('.toc-link').forEach(l => l.classList.remove('active'));
                            this.classList.add('active');
                            
                            history.pushState(null, null, targetId);
                        }
                    });
                });
                
                // Intersection observer for active states
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const id = entry.target.id;
                            document.querySelectorAll('.toc-link').forEach(link => {
                                const isActive = link.getAttribute('href') === '#' + id;
                                if (isActive) {
                                    link.classList.add('active');
                                } else {
                                    link.classList.remove('active');
                                }
                            });
                        }
                    });
                }, { rootMargin: '-20% 0px -70% 0px' });
                
                headings.forEach(heading => observer.observe(heading));
            }
        });
        
        // Share functionality
        function shareArticle() {
            if (navigator.share) {
                navigator.share({
                    title: document.title,
                    url: window.location.href
                });
            } else {
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Link copied to clipboard!');
                });
            }
        }
        
        // Article rating
        function rateArticle(helpful) {
            const message = document.getElementById('feedback-message');
            message.textContent = helpful ? 'Thanks! Glad this helped.' : 'Thanks for the feedback.';
            message.classList.remove('hidden');
            
            document.querySelectorAll('button[onclick^="rateArticle"]').forEach(btn => {
                btn.style.display = 'none';
            });
        }
    </script>
</x-app-layout>
