<x-app-layout>
    @section('title', 'XileRO - Classic Ragnarok Online Private Server | Free to Play PK Server')
    @section('description', 'Join XileRO, a free-to-play classic Ragnarok Online private server featuring intense PvP, War of Emperium, and unique gameplay mechanics. Download now and start your adventure!')
    @section('keywords', 'Ragnarok Online, RO Private Server, XileRO, Classic RO, PK Server, WoE Server, Free Ragnarok, MMORPG, Ragnarok Private Server 2024')

    @section('structured_data')
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "VideoGame",
        "name": "XileRO",
        "description": "XileRO is a classic Ragnarok Online private server featuring intense PvP, War of Emperium, and unique gameplay mechanics.",
        "genre": ["MMORPG", "Role-playing game", "PvP"],
        "gamePlatform": "PC",
        "applicationCategory": "Game",
        "operatingSystem": "Windows",
        "offers": {
            "@@type": "Offer",
            "price": "0",
            "priceCurrency": "USD",
            "availability": "https://schema.org/InStock"
        },
        "aggregateRating": {
            "@@type": "AggregateRating",
            "ratingValue": "4.8",
            "ratingCount": "500",
            "bestRating": "5",
            "worstRating": "1"
        }
    }
    </script>
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "WebSite",
        "name": "XileRO",
        "url": "{{ config('app.url') }}",
        "potentialAction": {
            "@@type": "SearchAction",
            "target": "{{ config('app.url') }}/item-database?search={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    @endsection

    <div id="particles-background" class="relative bg-clash-bg">
        {{-- Gold Particles --}}
        <div id="particles-container"></div>
        <style> 
            #particles-container {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                overflow: hidden;
                z-index: 1;
            }
            .gold-particle {
                position: absolute;
                bottom: -10px;
                background: #d4a84b;
                border-radius: 50%;
                opacity: 0;
                will-change: transform, opacity;
                animation: floatUp linear infinite;
            }
            @keyframes floatUp {
                0% {
                    opacity: 0;
                    transform: translateY(0) scale(0.5);
                }
                10% {
                    opacity: 0.8;
                }
                90% {
                    opacity: 0.6;
                }
                100% {
                    opacity: 0;
                    transform: translateY(-100vh) scale(1);
                }
            }
            .shooting-star {
                position: absolute;
                width: 100px;
                height: 2px;
                background: linear-gradient(to right, transparent, rgba(255,255,255,0.4), #fff);
                opacity: 0;
                will-change: transform, opacity;
                transform: rotate(-35deg);
                animation: shootingStar linear infinite;
            }
            @keyframes shootingStar {
                0% {
                    opacity: 0;
                    transform: rotate(-35deg) translateX(0);
                }
                1% {
                    opacity: 1;
                }
                14% {
                    opacity: 1;
                }
                15% {
                    opacity: 0;
                    transform: rotate(-35deg) translateX(200vw);
                }
                100% {
                    opacity: 0;
                    transform: rotate(-35deg) translateX(200vw);
                }
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('particles-container');
                const fragment = document.createDocumentFragment();

                // Gold particles
                for (let i = 0; i < 40; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'gold-particle';
                    particle.style.cssText = `
                        left: ${Math.random() * 100}%;
                        animation-duration: ${4 + Math.random() * 6}s;
                        animation-delay: ${-Math.random() * 10}s;
                        width: ${2 + Math.random() * 3}px;
                        height: ${2 + Math.random() * 3}px;
                        box-shadow: 0 0 ${3 + Math.random() * 4}px rgba(212, 168, 75, 0.6);
                    `;
                    fragment.appendChild(particle);
                }

                // Shooting stars scattered across full background
                for (let i = 0; i < 5; i++) {
                    const star = document.createElement('div');
                    star.className = 'shooting-star';
                    star.style.cssText = `
                        top: ${Math.random() * 100}%;
                        left: ${-20 + Math.random() * 80}%;
                        animation-duration: 20s;
                        animation-delay: ${i * 4 + Math.random() * 4}s;
                    `;
                    fragment.appendChild(star);
                }

                container.appendChild(fragment);
            });
        </script>

        <section class="shadow bg-transparent bg-right md:py-20 pb-12 md:pt-80 px-12 pt-40 relative z-10" id="hero-section">
            <div class="section-div text-gray-100 relative z-10">
                <span class="text-[100px] md:text-[140px] center-letter">X</span>
                <span class="text-[100px] md:text-[140px] center-letter">I</span>
                <span class="text-[100px] md:text-[140px] center-letter">L</span>
                <span class="text-[100px] md:text-[140px] center-letter">E</span>
                <span class="text-[100px] md:text-[140px] center-letter">R</span>
                <span class="text-[100px] md:text-[140px] center-letter">O</span>
            </div>
        </section>

    {{-- HyperDrive Section - Disabled
    <section id="read-the-rules" class="bg-transparent relative rounded-lg py-24 md:pt-32 hidden md:block z-10">
        <div class="z-0 absolute effect-light-blue-bang top-[20px] right-[140px]"></div>
        <div class="z-0 absolute effect-light-yellow-bang top-[20px] right-[180px]"></div>
        <div class="z-10 relative block-home max-w-screen-xl w-full mx-auto flex justify-between container md:text-center mb-0 bg-gray-900 p-4 py-8 rounded to-transparent">
            <div class="no-underline text-gray-100 hover:text-amber-300 font-bold text-2xl cursor-pointer">Connect with Low Ping, Globally</div>
            <div class="no-underline text-amber-500 hover:text-amber-300 font-bold text-2xl cursor-pointer" href=""><span class="text-gray-100">XileRO</span> | HyperDrive™  [<span class="text-gray-100 cursor-text">{{ config('xilero.hyperdrive.ip_address') }}</span>]</div>
        </div>
    </section>
    --}}

    </div>
{{--    <section id="rates" class="bg-clash-bg relative overflow-hidden py-16 md:pt-24 lg:pt-16">--}}
{{--        <div class="max-w-screen-xl w-full mx-auto lg:px-8 px-5">--}}
{{--            <h2>Quick Stats</h2>--}}
{{--            <p class="mt-6 text-gray-300 leading-relaxed">Welcome to the world of Xilero, where unique adventures await! If you're new to our server or looking to enhance your gameplay experience, you've come to the right place. Our Getting Started guides are crafted to help players of all levels navigate the distinct features and mechanics that set Xilero apart.</p>--}}

{{--            <div class="grid grid-cols-5 gap-8 text-gray-100 text-left">--}}
{{--            <div class="text-2xl col-span-2 md:col-span-1">--}}
{{--                <h3>Max Level</h3>--}}
{{--                <p class="text-5xl lg:text-7xl text-amber-200">{{ config('xilero.max_level') }}</p>--}}
{{--            </div>--}}
{{--            <div class="text-2xl col-span-2 md:col-span-1">--}}
{{--                <h3>Max Job</h3>--}}
{{--                <p class="text-5xl lg:text-7xl text-amber-200">{{ config('xilero.max_job') }}</p>--}}
{{--            </div>--}}
{{--            <div class="text-2xl col-span-2 md:col-span-1">--}}
{{--                <h3>Base EXP</h3>--}}
{{--                <p class="text-5xl lg:text-7xl text-amber-200">{{ config('xilero.base_exp') }}</p>--}}
{{--            </div>--}}
{{--            <div class="text-2xl col-span-2 md:col-span-1">--}}
{{--                <h3>Job EXP</h3>--}}
{{--                <p class="text-5xl lg:text-7xl text-amber-200">{{ config('xilero.job_exp') }}</p>--}}
{{--            </div>--}}
{{--            <div class="text-2xl col-span-2 md:col-span-1">--}}
{{--                <h3>Card Drops</h3>--}}
{{--                <p class="text-5xl lg:text-7xl text-amber-200">{{ config('xilero.card_drops') }}</p>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--            <p class="tracking-widest text-gray-100 text-xl mt-6 bg-gradient-to-r from-violet-800 to-transparent py-1 rounded px-2">Custom Built Mechanics & Gameplay</p>--}}
{{--        </div>--}}
{{--    </section>--}}

    <div class="line"></div>

    <div class="hidden md:block">
        <x-latest-post-preview lazy/>
        <div class="line"></div>
    </div>

    <section id="steps2play" class="bg-clash-bg relative overflow-hidden py-12 md:px-24">
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            @auth
                {{-- Section Header --}}
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-100 mb-2">Download XileRetro</h2>
                        <p class="text-gray-400">Choose your platform and start playing.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Windows Card --}}
                    <div class="card-glow-wrapper group transition-all duration-300 hover:-translate-y-1">
                        <div class="card-glow-inner p-6">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-14 h-14 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                                    <i class="fa fa-windows text-2xl text-blue-400"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-100">Windows</h3>
                                    <p class="text-gray-400 text-sm">Full client download</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                @foreach(\App\Models\Download::full()->get() as $download)
                                    <a href="{{ $download->download_url }}" target="_blank" rel="noopener" class="flex items-center justify-between p-3 rounded-lg bg-gray-800/50 border border-gray-700/50 hover:border-blue-500/30 hover:bg-gray-800 transition-colors no-underline group/btn">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-download text-gray-500 group-hover/btn:text-blue-400 transition-colors"></i>
                                            <span class="text-gray-300 group-hover/btn:text-gray-100 transition-colors">{{ $download->name }}</span>
                                        </div>
                                        <span class="text-xs text-gray-500">3GB</span>
                                    </a>
                                @endforeach
                            </div>

                            <p class="mt-5 text-gray-500 text-xs flex items-center gap-2">
                                <i class="fas fa-info-circle"></i>
                                Extract and run the patcher to update
                            </p>
                        </div>
                    </div>

                    {{-- Android Card --}}
                    <div class="card-glow-wrapper group transition-all duration-300 hover:-translate-y-1">
                        <div class="card-glow-inner p-6">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-14 h-14 rounded-lg bg-green-500/10 border border-green-500/20 flex items-center justify-center">
                                    <i class="fa fa-android text-2xl text-green-400"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-100">Android</h3>
                                    <p class="text-gray-400 text-sm">Play on the go</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                @foreach(\App\Models\Download::android()->get() as $download)
                                    <a href="{{ $download->download_url }}" target="_blank" rel="noopener" class="flex items-center justify-between p-3 rounded-lg bg-gray-800/50 border border-gray-700/50 hover:border-green-500/30 hover:bg-gray-800 transition-colors no-underline group/btn">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-download text-gray-500 group-hover/btn:text-green-400 transition-colors"></i>
                                            <span class="text-gray-300 group-hover/btn:text-gray-100 transition-colors">{{ $download->name }}</span>
                                        </div>
                                        <span class="text-xs text-gray-500">3MB</span>
                                    </a>
                                @endforeach
                            </div>

                            <p class="mt-5 text-gray-500 text-xs flex items-center gap-2">
                                <i class="fas fa-shield-alt"></i>
                                Supports Gepard protection & auto-updates
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <x-download-login-prompt />
            @endauth
        </div>
    </section>

    <div class="line"></div>

    {{-- <section id="mvprankingladder" class="container mx-auto grid">
        <h2>MVP Ladder</h2>
        @foreach (App\XileRO\MvpLadderRank::orderByDesc('day_kills')->limit(3)->get() as $rank)
            <div class="cols-span-1">
                <p>Player {{ $rank->name }}</p>
                <p>{{ $rank->day_kills }} MVP Kills Today!</p>
            </div>
        @endforeach
    </section> --}}

    <section id="getting-started" class="relative overflow-hidden py-12 bg-clash-bg md:px-24">
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            {{-- Section Header --}}
            <div class="flex items-end justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-gray-100 mb-2">Getting Started</h2>
                    <p class="text-gray-400 max-w-2xl">New to XileRO? These guides will help you navigate the unique features and mechanics that set our server apart.</p>
                </div>
                <a href="https://wiki.xilero.net" target="_blank" rel="noopener" class="hidden md:inline-flex items-center gap-2 text-amber-500 hover:text-amber-400 font-medium transition-colors">
                    Visit Wiki
                    <i class="fas fa-external-link-alt text-sm"></i>
                </a>
            </div>

            {{-- Cards Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $guides = [
                        ['title' => 'Server Information', 'description' => 'Rates, features & server details', 'image' => 'server-information.jpeg', 'url' => 'https://wiki.xilero.net/index.php?title=Server_Information'],
                        ['title' => 'Starter Guides', 'description' => 'Essential packages for new players', 'image' => 'starter-packages.jpeg', 'url' => 'https://wiki.xilero.net/index.php?title=Newbie_Center'],
                        ['title' => 'Leveling Spots', 'description' => 'Best areas for progression', 'image' => 'leveling-areas.jpeg', 'url' => 'https://wiki.xilero.net/index.php?title=Leveling_Spots'],
                        ['title' => 'Discord Community', 'description' => 'Join discussions & get help', 'image' => 'discord-community.jpeg', 'url' => 'https://discord.gg/hp7CS6k'],
                        ['title' => 'Donation Rewards', 'description' => 'Support the server & earn rewards', 'image' => 'donation-help.jpeg', 'url' => 'https://wiki.xilero.net/index.php?title=Donation'],
                        ['title' => 'MVP System', 'description' => 'Boss hunting & rankings', 'image' => 'mvp-ranking.jpeg', 'url' => 'https://wiki.xilero.net/index.php?title=MVP'],
                        ['title' => 'Random Weapons', 'description' => 'Unique randomized loot system', 'image' => 'randomized-weapon-loots.jpeg', 'url' => 'https://wiki.xilero.net/index.php?title=MVP'],
                        ['title' => 'Wiki Database', 'description' => 'Complete knowledge base', 'image' => 'wikipedia-knowledge.jpeg', 'url' => 'https://wiki.xilero.net'],
                    ];
                @endphp

                @foreach($guides as $guide)
                    <a href="{{ $guide['url'] }}" target="_blank" rel="noopener" class="group card-glow-wrapper transition-all duration-300 hover:-translate-y-1 no-underline">
                        <div class="card-glow-inner">
                            {{-- Image --}}
                            <div class="relative h-36 overflow-hidden">
                                <img
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    src="{{ asset('assets/getting-started/' . $guide['image']) }}"
                                    alt="{{ $guide['title'] }}"
                                >
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/50 to-transparent"></div>
                            </div>

                            {{-- Content --}}
                            <div class="p-4">
                                <h3 class="text-base font-semibold text-gray-100 mb-1 group-hover:text-amber-400 transition-colors">
                                    {{ $guide['title'] }}
                                </h3>
                                <p class="text-gray-500 text-sm">
                                    {{ $guide['description'] }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Mobile Wiki Link --}}
            <div class="mt-8 text-center md:hidden">
                <a href="https://wiki.xilero.net" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-amber-500 hover:text-amber-400 font-medium transition-colors">
                    Visit Wiki
                    <i class="fas fa-external-link-alt text-sm"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- <section id="important-links">
        <div class="relative overflow-hidden py-16 md:pt-48">
            <span class="hidden absolute bg-radial-gradient opacity-[.15] pointer-events-none lg:inline-flex right-[-20%] top-0 w-[640px] h-[640px]"></span>
            <div class="max-w-screen-xl w-full mx-auto px-5">
                <h2 class="text-4xl font-bold max-w-lg md:text-4xl">Donation & Donation-Free Server Economics</h2>
                <div class="mt-14 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                    <svg class="text-rose-500 w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <h3 class="mt-5 text-xl font-bold">Donate</h3>
                        <p class="mt-4 text-gray-700 text-sm leading-relaxed">Earn ubers by supporting the server and get rewarded just remember we only reward uber tokens that can be spent in the uber store and nothing else.</p>
                        <a class="group relative inline-flex border border-red-600 focus:outline-hidden mt-6" href="https://wiki.xilero.net/index.php?title=Donation">
                            <span class="w-full inline-flex items-center justify-center self-stretch px-4 py-2 text-sm text-red-600 text-center font-bold uppercase bg-white ring-1 ring-red-600 ring-offset-1 transform transition-transform group-hover:-translate-y-1 group-hover:-translate-x-1 group-focus:-translate-y-1 group-focus:-translate-x-1">
                                Donate Now
                            </span>
                        </a>
                    </div>
                    <div>
                        <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                        <h3 class="mt-5 text-xl font-bold">Zeny Purchase</h3>
                        <p class="mt-4 text-gray-700 text-sm leading-relaxed">Don't want to ever donate? Well you do not have too just spend some time in game, get to know others and you will have enough zeny to purchase ubers in no time.</p>
                        <a class="group relative inline-flex border border-red-600 focus:outline-hidden mt-6" href="https://wiki.xilero.net/index.php?title=Donation">
                            <span class="w-full inline-flex items-center justify-center self-stretch px-4 py-2 text-sm text-red-600 text-center font-bold uppercase bg-white ring-1 ring-red-600 ring-offset-1 transform transition-transform group-hover:-translate-y-1 group-hover:-translate-x-1 group-focus:-translate-y-1 group-focus:-translate-x-1">
                                Learn How
                            </span>
                        </a>
                    </div>
                    <!-- <div>
                        <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        <h3 class="mt-5 text-xl font-bold">Cryptocurrencies</h3>
                        <p class="mt-4 text-gray-700 text-sm leading-relaxed"><a class="underline" href="/docs/broadcasting">Laravel Echo</a> and event broadcasting make it a cinch to build modern, realtime user experiences. Create amazing realtime applications while powering your WebSockets with pure PHP, Node.js, or serverless solutions like <a class="underline" href="https://pusher.com">Pusher</a> and <a class="underline" href="https://ably.com">Ably</a>.</p>
                    </div> -->
                </div>
            </div>
        </div>
    </section> --}}

    <div class="line"></div>

    <section id="uber-store" class="bg-clash-bg mx-auto py-12 md:px-24">
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            {{-- Section Header --}}
            <div class="flex items-end justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-gray-100 mb-2">Uber Store</h2>
                    <p class="text-gray-400">Most popular items from the shop. Earn Ubers by donating or trading in-game.</p>
                </div>
                <a href="{{ route('donate-shop') }}" class="hidden md:inline-flex items-center gap-2 text-amber-500 hover:text-amber-400 font-medium transition-colors">
                    View all items
                    <i class="fas fa-arrow-right text-sm"></i>
                </a>
            </div>

            {{-- Items Grid --}}
            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($popularUberItems as $shopItem)
                    <a href="{{ route('donate-shop') }}" class="group card-glow-wrapper transition-all duration-300 hover:-translate-y-1 no-underline">
                        <div class="card-glow-inner p-4">
                            <div class="flex gap-4">
                                {{-- Item Image --}}
                                <div class="shrink-0 w-16 h-16 bg-gray-800/80 rounded-lg overflow-hidden flex items-center justify-center border border-gray-700/50 group-hover:border-amber-500/30 transition-colors">
                                    @if ($shopItem->item)
                                        <img
                                            src="{{ $shopItem->item->collection() }}"
                                            alt="{{ $shopItem->item->name }}"
                                            class="max-h-full max-w-full object-contain transition-transform duration-300 group-hover:scale-110"
                                            onerror="this.onerror=null; this.src='{{ $shopItem->item->icon() }}';"
                                            loading="lazy"
                                        >
                                    @else
                                        <i class="fas fa-box text-gray-600 text-xl"></i>
                                    @endif
                                </div>

                                {{-- Item Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="font-semibold text-gray-100 truncate group-hover:text-amber-400 transition-colors">{{ $shopItem->display_name }}</h3>
                                        @if ($shopItem->exclusive_server)
                                            <span class="shrink-0 text-[10px] font-medium px-1.5 py-0.5 rounded border {{ $shopItem->is_xilero ? 'bg-amber-500/10 text-amber-400 border-amber-500/30' : 'bg-blue-500/10 text-blue-400 border-blue-500/30' }}">
                                                {{ $shopItem->exclusive_server }}
                                            </span>
                                        @endif
                                    </div>

                                    @if ($shopItem->item?->description)
                                        <p class="text-xs text-gray-500 line-clamp-1">{!! $shopItem->item->formattedDescription() !!}</p>
                                    @elseif ($shopItem->item?->type)
                                        <p class="text-xs text-gray-500">
                                            {{ $shopItem->item->type }}{{ $shopItem->item->subtype ? ' / ' . $shopItem->item->subtype : '' }}
                                        </p>
                                    @endif

                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="text-amber-400 font-bold text-sm">
                                            {{ $shopItem->uber_cost }} {{ Str::plural('Uber', $shopItem->uber_cost) }}
                                        </span>
                                        @if ($shopItem->stock !== null)
                                            @if ($shopItem->stock > 0)
                                                <span class="text-xs text-green-400/80">{{ $shopItem->stock }} left</span>
                                            @else
                                                <span class="text-xs text-red-400">Sold Out</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Mobile View All --}}
            <div class="mt-8 text-center md:hidden">
                <a href="{{ route('donate-shop') }}" class="inline-flex items-center gap-2 text-amber-500 hover:text-amber-400 font-medium transition-colors">
                    View all items
                    <i class="fas fa-arrow-right text-sm"></i>
                </a>
            </div>
        </div>
    </section>

    <div class="line"></div>

    <section id="woe-times" class="bg-clash-bg hidden lg:block relative overflow-hidden py-12 pb-32 md:px-24">
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            {{-- Section Header --}}
            <div class="flex items-end justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-gray-100 mb-2">War of Emperium</h2>
                    <p class="text-gray-400 max-w-2xl">Battle schedules across all timezones. Rally your guild and conquer the castles.</p>
                </div>
                <a href="https://wiki.xilero.net/index.php?title=WoE" target="_blank" rel="noopener" class="hidden md:inline-flex items-center gap-2 text-amber-500 hover:text-amber-400 font-medium transition-colors">
                    WoE Guide
                    <i class="fas fa-external-link-alt text-sm"></i>
                </a>
            </div>

            {{-- Castles Grid --}}
            <div class="grid grid-cols-{{ $castles->count() }} gap-4">
                @foreach ($castles as $castle)
                    <div class="card-glow-wrapper group">
                        <div class="card-glow-inner p-5">
                            {{-- Castle Header --}}
                            <div class="flex items-center justify-between mb-5">
                                <h3 class="text-xl font-bold text-gray-100">{{ $castle->name }}</h3>
                                @if(!is_null($castle->guild) && $castle->guild->hasEmblem())
                                    <div class="w-8 h-8 rounded bg-gray-800 border border-gray-700" style="background: url('{{ $castle->guild->emblem }}') center/contain no-repeat;"></div>
                                @endif
                            </div>

                            {{-- Timezone Schedule --}}
                            <div class="space-y-3">
                                @foreach(config('castles.timezones') as $timezone)
                                    <div class="flex items-center gap-3 p-2.5 rounded-lg bg-gray-800/40 border border-gray-700/30">
                                        <div class="w-8 h-8 rounded bg-gray-700/50 flex items-center justify-center shrink-0">
                                            <i class="fas fa-globe text-gray-500 text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-gray-300 text-sm font-medium truncate">{{ str_replace('_', ' ', basename($timezone)) }}</p>
                                            @foreach(config("castles.prontera.{$castle->name}.day") as $day)
                                                @php
                                                    $date = new DateTime();
                                                    $date->modify("next {$day}");
                                                    $time = DateTime::createFromFormat("H:i", config("castles.prontera.{$castle->name}.time"));
                                                    $date->setTime($time->format('H'), $time->format('i'))->modify(config('castles.modifier'));
                                                @endphp
                                                <p class="text-amber-500/80 text-xs">{{ $date->setTimezone(new DateTimeZone($timezone))->format("l, H:i A") }}</p>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="line"></div>

    {{-- ANDOID SECTION REMOVED
        <section class="my-24 px-3 sm:px-0">
            <div id="android" class="container mx-auto">
                <h2 class="mt-0">Want to play our Android Beta version?</h2>
                <div class="grid grid-cols-3 gap-12">
                    <div class="col-span-3 mb-10 lg:col-span-1">
                        <h3>Get started</h3>
                        <p>Start downloading by visiting this the xileretro website with your android device and clicking the link below.</p>
                        <a href="{{ config('downloads.android.link') }}" class="btn bg-blue-500 text-white no-underline">XileRetro {{ config('downloads.android.title') }} APK v{{ config('downloads.android.version') }}</a>
                    </div>
                    <div class="col-span-3 mb-5 lg:col-span-1">
                        <h3>OTA Updates</h3>
                        <p>Our android application delivers updates over the air meaning you never have to worry about outdated version.</p>
                    </div>
                    <div class="col-span-3 mb-10 lg:col-span-1">
                        <h3>Bugs and Support</h3>
                        <p>We actively fix bugs and provide support to our android application through our discord channels.</p>
                        <a href="https://discord.gg/hp7CS6k" class="btn bg-gray-500 text-white no-underline">Report android issues on our discord</a>
                    </div>
                </div>
            </div>
        </section>
    --}}

{{--    <div class="line"></div>--}}

{{--    <section id="read-the-rules" class="bg-black relative overflow-hidden py-16 md:pt-32 hidden md:block">--}}
{{--        <div class="max-w-screen-xl w-full mx-auto px-5 container text-left md:text-center mb-20">--}}
{{--            <h2 class="mb-8 tracking-widest important-title text-blue-500" style="font-size: 2.5em"><a target="_blank" rel="noopener noreferrer" class="no-underline text-amber-500 hover:text-amber-300 font-bold" href=""><span class="text-gray-100">XileRO PK</span> | Third Jobs</a></h2>--}}
{{--            <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5 mt-20">--}}
{{--                <div class="grid grid-cols-3 gap-12 text-gray-100 text-center mt-8">--}}
{{--                    <div class="bg-gray-800 bg-opacity-90 rounded text-gray-100 hover:bg-amber-500 hover:text-gray-900">--}}
{{--                        <a target="_blank" rel="noopener noreferrer" title="XileRO PK Third Jobs Download" aria-label="XileRO PK Third Jobs Download" href="https://drive.google.com/drive/folders/1EGeKownNt1cYne1e173OshhbYj31-mh-?usp=sharing">--}}
{{--                            <img class="object-cover w-full rounded h-44" style="margin:0" src="{{ asset('assets/third-jobs/download.webp') }}" alt="XileRO PK Third Jobs Download Image">--}}
{{--                            <div class="p-4">--}}
{{--                                XileRO PK Download--}}
{{--                            </div>--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                    <div class="bg-gray-800 bg-opacity-90 rounded text-gray-100 hover:bg-amber-500 hover:text-gray-900">--}}
{{--                        <a target="_blank" rel="noopener noreferrer" title="XileRO PK Third Jobs Facebook" aria-label="XileRO PK Third Jobs Facebook" href="https://www.facebook.com/groups/670800967076806/">--}}
{{--                            <img class="object-cover w-full rounded h-44" style="margin:0" src="{{ asset('assets/third-jobs/facebook.jpeg') }}" alt="XileRO PK Third Jobs Facebook Image">--}}
{{--                            <div class="p-4">--}}
{{--                                XileRO PK Facebook--}}
{{--                            </div>--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                    <div class="bg-gray-800 bg-opacity-90 rounded text-gray-100 hover:bg-amber-500 hover:text-gray-900">--}}
{{--                        <a target="_blank" rel="noopener noreferrer" title="XileRO PK Third Jobs Discord" aria-label="XileRO PK Third Jobs Discord" href="https://discord.com/invite/cFd4FZupDV">--}}
{{--                            <img class="object-cover w-full rounded h-44" style="margin:0" src="{{ asset('assets/third-jobs/discord.jpeg') }}" alt="XileRO PK Third Jobs Discord Image">--}}
{{--                            <div class="p-4">--}}
{{--                                XileRO PK Discord--}}
{{--                            </div>--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </section>--}}

    {{-- <section id="communities" class="p-20">
        <h2>Community</h2>

        <div id="platforms" class="mb-8">
            <h3>Platforms</h3>
            <h4>Participate in player discussions, future content and guild communications.</h4>
            <div class="grid grid-cols-10 col-gap-8">
                <div class="col-span-7">
                    <iframe src="https://titanembeds.com/embed/702319926110584943" height="500" width="100%" frameborder="0"></iframe>
                </div>
                <div class="col-span-3">
                    <iframe src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fxileretro&tabs=timeline&width=340&height=500&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId=661838800646818" width="100%" height="500" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media"></iframe>
                </div>
            </div>
        </div> --}}

    {{-- <div id="loading-screens" class="mb-8">
        <h3>Loading Screens</h3>
        <h4>Imagery created by talented players</h4>
        <div class="grid grid-cols-4 col-gap-4">
            <div class="col-span1">
                <img style="margin:0 auto 0 auto" class="loading-screen" src="/images/loading/loading00@2x.png" alt="">
            </div>
            <div class="col-span1">
                <img style="margin:0 auto 0 auto" class="loading-screen" src="/images/loading/loading06@2x.png" alt="">
            </div>
            <div class="col-span1">
                <img style="margin:0 auto 0 auto" class="loading-screen" src="/images/loading/loading08@2x.png" alt="">
            </div>
            <div class="col-span1">
                <img style="margin:0 auto 0 auto" class="loading-screen" src="/images/loading/loading16@2x.png" alt="">
            </div>
        </div>
    </div> --}}

    {{-- <div id="streams" class="mb-8">
        <div class="grid grid-cols-2 gap-8">
            <div class="col-span-1">
                <iframe src="https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2Fitsmegcmc%2Fvideos%2F337497594283701%2F&show_text=true&width=734&appId=661838800646818&height=661" width="600" height="700" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" allowFullScreen="true"></iframe>
            </div>
            <div class="col-span-1">
                <iframe src="https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2FAkarenochoa26%2Fvideos%2F3175468255893900%2F&show_text=true&width=734&appId=661838800646818&height=580" width="600" height="700" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allow="encrypted-media" allowFullScreen="true"></iframe>
            </div>
        </div>
    </div> --}}

    {{-- <section id="XileRO-PK" class="relative overflow-hidden py-16 md:pt-32 bg-black">
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            <h2 class="text-4xl font-bold max-w-lg md:text-4xl text-gray-100">XileRO PK | <span class="text-amber-500">Third Jobs</span></h2>
            <div class="grid grid-cols-3 gap-12 text-gray-100 text-center mt-8">
                <div class="bg-gray-800 bg-opacity-90 rounded text-gray-100">
                    <div class="">
                        <img class="object-cover w-full rounded h-44" style="margin:0" src="https://wallpapercg.com/media/ts_2x/11480.webp" alt="Server information Image">
                        <div class="p-4">
                            XileRO PK Download
                        </div>
                    </div>
                </div>
                <div class="bg-gray-800 bg-opacity-90 rounded text-gray-100">
                    <div class="">
                        <img class="object-cover w-full rounded h-44" style="margin:0" src="https://wallpapers.com/images/hd/sword-art-online-wallpaper-javjk4u0ar7tbyeu.jpg" alt="Server information Image">
                        <div class="p-4">
                            XileRO PK Register
                        </div>
                    </div>
                </div>
                <div class="bg-gray-800 bg-opacity-90 rounded text-gray-100">
                    <div class="">
                        <img class="object-cover w-full rounded h-44" style="margin:0" src="https://wallpapers.com/images/hd/discord-logo-geometric-art-5barh6w9jxj5mhzw.jpg" alt="Server information Image">
                        <div class="p-4">
                            XileRO PK Discord
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-4 gap-12 mt-14">
                <div class="col-span-4 md:col-span-2 lg:col-span-1 bg-gray-900 hover:bg-gray-800 rounded">
                    <a target="_blank" href="https://wiki.xilero.net/index.php?title=Server_Information">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="https://swall.teahub.io/photos/small/99-999290_ragnarok-online-valkyrie.jpg" alt="Server information Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">Server<br> Information & Features</h3>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section> --}}


</x-app-layout>
