<x-app-layout>
    @section("title", "XileRO Wiki - Guides, Tutorials & Game Information")
    @section('description', 'Complete XileRO Wiki with beginner guides, class builds, quest walkthroughs, and server information. Everything you need to master Ragnarok Online.')
    @section('keywords', 'XileRO wiki, Ragnarok Online guide, RO tutorial, class builds, quest guide, leveling guide, XileRO help')

    {{-- Hero Section with Search --}}
    <section class="bg-gray-950 pt-20 lg:pt-24 py-16 md:py-20">
        <div class="max-w-screen-xl w-full mx-auto px-5 text-center">
            <div class="mb-8">
                <h1 class="text-5xl lg:text-6xl font-bold text-gray-100 mb-6 leading-tight">
                    XileRO <span class="text-amber-500">Wiki</span>
                </h1>
                <p class="text-xl lg:text-2xl text-gray-300 font-normal max-w-3xl mx-auto leading-relaxed">
                    Your comprehensive guide to XileRO's classic Ragnarok Online experience
                </p>
            </div>
            
            
            {{-- Quick Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
                <div class="bg-gray-900 border border-gray-800 p-4 rounded text-center">
                    <div class="text-2xl lg:text-3xl font-bold text-amber-500">50x</div>
                    <div class="text-sm text-gray-400">EXP Rate</div>
                </div>
                <div class="bg-gray-900 border border-gray-800 p-4 rounded text-center">
                    <div class="text-2xl lg:text-3xl font-bold text-amber-500">25x</div>
                    <div class="text-sm text-gray-400">Drop Rate</div>
                </div>
                <div class="bg-gray-900 border border-gray-800 p-4 rounded text-center">
                    <div class="text-2xl lg:text-3xl font-bold text-amber-500">99/70</div>
                    <div class="text-sm text-gray-400">Max Level</div>
                </div>
                <div class="bg-gray-900 border border-gray-800 p-4 rounded text-center">
                    <div class="text-2xl lg:text-3xl font-bold text-amber-500">24/7</div>
                    <div class="text-sm text-gray-400">Online</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="bg-gray-950 py-16 md:py-20">
        <div class="max-w-screen-xl w-full mx-auto px-5">
            
            {{-- Categories Grid --}}
            <div class="mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-100 text-center mb-4">Browse by Category</h2>
                <p class="text-lg text-gray-300 text-center mb-12 max-w-2xl mx-auto">
                    Explore our comprehensive guides and documentation organized by topic
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {{-- Getting Started --}}
                    <div class="group">
                        <a href="/wiki/getting-started" class="bg-gray-900 border border-gray-800 hover:bg-gray-800 p-6 rounded block transition-colors duration-200">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-green-600 rounded flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-100">Getting Started</h3>
                            </div>
                            <p class="text-gray-400 text-sm leading-relaxed mb-3">
                                New to XileRO? Start here with our comprehensive beginner's guide to get you up and running.
                            </p>
                            <span class="text-amber-500 text-sm font-medium">Learn More →</span>
                        </a>
                    </div>

                    {{-- Server Information --}}
                    <div class="group">
                        <a href="/wiki/server-info" class="bg-gray-900 border border-gray-800 hover:bg-gray-800 p-6 rounded block transition-colors duration-200">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-blue-600 rounded flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-100">Server Information</h3>
                            </div>
                            <p class="text-gray-400 text-sm leading-relaxed mb-3">
                                Detailed server rates, features, and mechanics that make XileRO unique and exciting.
                            </p>
                            <span class="text-amber-500 text-sm font-medium">Explore Features →</span>
                        </a>
                    </div>

                    {{-- Class Guides --}}
                    <div class="group">
                        <a href="/wiki/classes" class="bg-gray-900 border border-gray-800 hover:bg-gray-800 p-6 rounded block transition-colors duration-200">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-purple-600 rounded flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-100">Class Guides</h3>
                            </div>
                            <p class="text-gray-400 text-sm leading-relaxed mb-3">
                                In-depth guides for all job classes including builds, skills, and gameplay strategies.
                            </p>
                            <span class="text-amber-500 text-sm font-medium">View Guides →</span>
                        </a>
                    </div>

                    {{-- Commands --}}
                    <div class="group">
                        <a href="/wiki/commands" class="bg-gray-900 border border-gray-800 hover:bg-gray-800 p-6 rounded block transition-colors duration-200">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-orange-600 rounded flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-100">Commands Guide</h3>
                            </div>
                            <p class="text-gray-400 text-sm leading-relaxed mb-3">
                                Complete list of available in-game commands to enhance your gameplay experience.
                            </p>
                            <span class="text-amber-500 text-sm font-medium">Browse Commands →</span>
                        </a>
                    </div>

                    {{-- Community --}}
                    <div class="group">
                        <a href="/wiki/community" class="bg-gray-900 border border-gray-800 hover:bg-gray-800 p-6 rounded block transition-colors duration-200">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-indigo-600 rounded flex items-center justify-center mr-4">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-100">Community</h3>
                            </div>
                            <p class="text-gray-400 text-sm leading-relaxed mb-3">
                                Join our community, find guilds, participate in events, and connect with other players.
                            </p>
                            <span class="text-amber-500 text-sm font-medium">Get Involved →</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Featured Articles --}}
                <div class="lg:col-span-2">
                    <h3 class="text-2xl font-bold text-gray-100 mb-6">Featured Articles</h3>
                    <div class="space-y-6">
                        <div class="bg-gray-900 border border-gray-800 p-6 rounded">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-xl font-bold text-gray-100 mb-2">
                                        <a href="/wiki/getting-started" class="hover:text-amber-500 transition-colors">Complete Beginner's Guide</a>
                                    </h4>
                                    <p class="text-gray-300 mb-4">Everything you need to know to start your journey on XileRO, from account creation to your first adventures.</p>
                                    <div class="flex items-center text-sm text-gray-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Updated Dec 15, 2024
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <span class="px-3 py-1 border border-green-600 text-green-400 text-xs font-medium rounded">New Player</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-900 border border-gray-800 p-6 rounded">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-xl font-bold text-gray-100 mb-2">
                                        <a href="/wiki/pvp-guide" class="hover:text-amber-500 transition-colors">PvP Combat Guide</a>
                                    </h4>
                                    <p class="text-gray-300 mb-4">Master the art of player vs player combat with our comprehensive PvP strategies and tips.</p>
                                    <div class="flex items-center text-sm text-gray-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Updated Dec 12, 2024
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <span class="px-3 py-1 border border-red-600 text-red-400 text-xs font-medium rounded">Combat</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-900 border border-gray-800 p-6 rounded">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-xl font-bold text-gray-100 mb-2">
                                        <a href="/wiki/equipment-guide" class="hover:text-amber-500 transition-colors">Equipment & Enchanting</a>
                                    </h4>
                                    <p class="text-gray-300 mb-4">Learn about equipment grades, enchantments, and how to optimize your gear for maximum effectiveness.</p>
                                    <div class="flex items-center text-sm text-gray-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Updated Dec 10, 2024
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <span class="px-3 py-1 border border-purple-600 text-purple-400 text-xs font-medium rounded">Equipment</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-8">
                    {{-- Quick Links --}}
                    <div class="bg-gray-900 border border-gray-800 p-6 rounded">
                        <h3 class="text-xl font-bold text-gray-100 mb-6">Quick Links</h3>
                        <div class="space-y-3">
                            <a href="https://discord.gg/hp7CS6k" class="group flex items-center px-3 py-3 text-gray-300 hover:text-amber-500 hover:bg-gray-800 rounded transition-all duration-200">
                                <svg class="w-5 h-5 mr-3 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M16.942 4.967a13.533 13.533 0 00-3.332-1.033.05.05 0 00-.053.025 9.441 9.441 0 00-.414.853 12.476 12.476 0 00-3.744 0 9.111 9.111 0 00-.421-.853.052.052 0 00-.053-.025 13.499 13.499 0 00-3.331 1.033.047.047 0 00-.022.018C1.47 8.252.828 11.455 1.15 14.611a.056.056 0 00.021.038 13.581 13.581 0 004.089 2.066.052.052 0 00.057-.019 9.63 9.63 0 00.836-1.359.051.051 0 00-.028-.072 8.943 8.943 0 01-1.277-.608.052.052 0 01-.006-.087c.086-.065.171-.133.253-.201a.05.05 0 01.052-.007c2.679 1.223 5.578 1.223 8.23 0a.05.05 0 01.053.006c.082.068.167.137.254.202a.052.052 0 01-.005.087c-.408.238-.834.44-1.278.607a.051.051 0 00-.028.073c.235.464.505.904.836 1.359a.052.052 0 00.057.019 13.546 13.546 0 004.094-2.066.052.052 0 00.021-.037c.378-3.648-.63-6.815-2.666-9.626a.041.041 0 00-.021-.019z"/>
                                </svg>
                                <span class="font-semibold">Discord Server</span>
                            </a>
                            <a href="/download" class="group flex items-center px-3 py-3 text-gray-300 hover:text-amber-500 hover:bg-gray-800 rounded transition-all duration-200">
                                <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="font-semibold">Download Client</span>
                            </a>
                            <a href="/register" class="group flex items-center px-3 py-3 text-gray-300 hover:text-amber-500 hover:bg-gray-800 rounded transition-all duration-200">
                                <svg class="w-5 h-5 mr-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                <span class="font-semibold">Create Account</span>
                            </a>
                        </div>
                    </div>

                    {{-- Recent Updates --}}
                    <div class="bg-gray-900 border border-gray-800 p-6 rounded">
                        <h3 class="text-xl font-bold text-gray-100 mb-6">Recent Updates</h3>
                        <div class="space-y-4">
                            <div class="border-l-2 border-amber-500 pl-4">
                                <h4 class="font-semibold text-gray-100">Patch 2024.12.15</h4>
                                <p class="text-sm text-gray-400 mb-2">New headgears, PvP balance, bug fixes</p>
                                <span class="text-xs text-gray-500">2 days ago</span>
                            </div>
                            <div class="border-l-2 border-blue-500 pl-4">
                                <h4 class="font-semibold text-gray-100">Christmas Event</h4>
                                <p class="text-sm text-gray-400 mb-2">Special holiday event with exclusive rewards</p>
                                <span class="text-xs text-gray-500">1 week ago</span>
                            </div>
                            <div class="border-l-2 border-purple-500 pl-4">
                                <h4 class="font-semibold text-gray-100">New Instance</h4>
                                <p class="text-sm text-gray-400 mb-2">Challenge dungeon with rare drops</p>
                                <span class="text-xs text-gray-500">2 weeks ago</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-app-layout>