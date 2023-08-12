@extends('layouts.app')

@section('content')

    <section class="shadow landing bg-cover pt-16">
        <div class="container grid px-3 sm:px-0 grid-cols-5 gap-4 mx-auto">
            <div class="col-span-5 lg:col-span-3 pt-20 pb-4 pr-6">
                <div class="xl:mr-30 prose text-white p-8 rounded bg-gray-900 text-gray-300 bg-opacity-70">
                    @if (session('message'))
                        <div class="alert alert-info">
                            <h1 class="mb-6 text-gray-500 text-3xl">{{ session('message') }}</h1>
                        </div>
                    @endif
                    <h1 class="mb-6 text-white text-3xl">XileRO PK Retro | <span class="text-amber-500">No Third Jobs</span></h1>

                    <p class="text-xl text-gray-200">Private Ragnarok Online Server</p>
                    <p>
                        Welcome to the realm of fantasy and roleplay. Where you will be faced with monsters, magic adventure, and ultimately each other. Find friends, companions, foes, and so much more. Fight for leadership, power and glory. Rest at home here in the world of XileRO
                    </p>
                    <p>
                        We secured the platform using the latest Gepard 3.0 security systems that helps prevents hacks
                        and macros to ultimately provide a solid foundation for future growth
                        internally and externally. We are not just here for the old players,
                        we are also here for the new~ So log in and pawn some noobs!!
                    </p>
                    <div class="flex flex-col my-10 quick-links">
                        <a href="#steps2play" class="w-full no-underline">
                            <button id="hero-registration" class="btn mt-4 xilero-button">
                                <!-- <svg class="w-4 h-4 mr-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg> -->
                                <span>Create a Game Account</span>
                            </button>
                        </a>
                        <a href="{{ config('downloads.full')[0]['link'] }}" target="_blank" class="w-full no-underline hidden lg:block">
                            <button id="hero-download-full-client" class="btn mt-4 xilero-button">
                                <span>{{ config('downloads.full')[array_key_first(config('downloads.full'))]['name'] }}</span>
                            </button>
                        </a>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-8 my-4 text-5xl social-buttons">
                    <a href="#important-links" class="text-gray-700 hover:text-cool-gray-900">
                        <i class="fas fa-angle-double-down"></i>
                    </a>
                    {{-- <div class="flex flex-row">
                        <a href="https://www.facebook.com/xileretro" class="text-blue-500 hover:text-blue-800">
                            <div class="mr-6 socal-facebook text-blue-500 hover:text-blue-800"><i class="fab fa-facebook"></i></div>
                        </a>
                        <a href="https://discord.gg/hp7CS6k" class="text-indigo-500 hover:text-indigo-800">
                            <div class="socal-discord text-indigo-500 hover:text-indigo-800"><i class="fab fa-discord"></i></div>
                        </a>
                    </div> --}}
                </div>
            </div>
        </div>
    </section>

    <section id="steps2play" class="bg-black relative overflow-hidden py-16 md:pt-24 lg:pt-32">
    <div class="hidden lg:block absolute -right-2 bottom-40 pointer-events-none">
            <svg x-data="{
        initializeAnimation: false,
        init() {
            setTimeout(() => {
                this.initializeAnimation = true;
            }, 2000);
        },
    }" :class="initializeAnimation ? 'animate-cube' : ''" class="text-red-600 animate-cube" width="46" height="53" viewBox="0 0 46 53" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="m23.102 1 22.1 12.704v25.404M23.101 1l-22.1 12.704v25.404" stroke="currentColor" stroke-width="1.435" stroke-linejoin="bevel"></path><path d="m45.202 39.105-22.1 12.702L1 39.105" stroke="currentColor" stroke-width="1.435" stroke-linejoin="bevel"></path><path transform="matrix(.86698 .49834 .00003 1 1 13.699)" stroke="currentColor" stroke-width="1.435" stroke-linejoin="bevel" d="M0 0h25.491v25.405H0z"></path><path transform="matrix(.86698 -.49834 -.00003 1 23.102 26.402)" stroke="currentColor" stroke-width="1.435" stroke-linejoin="bevel" d="M0 0h25.491v25.405H0z"></path><path transform="matrix(.86701 -.49829 .86701 .49829 1 13.702)" stroke="currentColor" stroke-width="1.435" stroke-linejoin="bevel" d="M0 0h25.491v25.491H0z"></path>
</svg>
        </div>
    <span class="hidden absolute bg-radial-gradient opacity-[.15] pointer-events-none lg:inline-flex right-[-20%] top-0 w-[640px] h-[940px]"></span>
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            <livewire:register/>
        </div>
    </section>

    {{-- <section id="mvprankingladder" class="container mx-auto grid">
        <h2>MVP Ladder</h2>
        @foreach (App\Ragnarok\MvpLadderRank::orderByDesc('day_kills')->limit(3)->get() as $rank)
            <div class="cols-span-1">
                <p>Player {{ $rank->name }}</p>
                <p>{{ $rank->day_kills }} MVP Kills Today!</p>
            </div>
        @endforeach
    </section> --}}

    <section id="important-links" class="relative overflow-hidden py-16 md:pt-32 bg-black">
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            <h2 class="text-4xl font-bold max-w-lg md:text-4xl text-gray-100">Getting Started</h2>
            <div class="grid grid-cols-4 gap-12 mt-14">
                <div class="col-span-4 md:col-span-2 lg:col-span-1 bg-gray-900 hover:bg-gray-800 rounded">
                    <a target="_blank" href="https://wiki.xileretro.net/index.php?title=Server_Information">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="https://swall.teahub.io/photos/small/99-999290_ragnarok-online-valkyrie.jpg" alt="Server information Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">Server<br> Information & Features</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1 bg-gray-900 hover:bg-gray-800 rounded">
                    <a target="_blank" href="https://wiki.xileretro.net/index.php?title=Newbie_Center">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="https://get.wallhere.com/photo/anime-Sword-Art-Online-Kirito-Sword-Art-Online-sword-1859509.jpg" alt="Server information Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">Starter<br>Packages & <br>Guides</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1 bg-gray-900 hover:bg-gray-800 rounded">
                    <a target="_blank" href="https://wiki.xileretro.net/index.php?title=Leveling_Spots">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="https://swall.teahub.io/photos/small/99-994510_photo-wallpaper-forest-flower-grass-elf-art-girl.jpg" alt="Server information Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">Leveling<br>Areas & <br>Progression</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1 bg-gray-900 hover:bg-gray-800 rounded">
                    <a target="_blank" href="https://wiki.xileretro.net/index.php?title=Leveling_Spots">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="https://e0.pxfuel.com/wallpapers/487/916/desktop-wallpaper-the-best-discord-themes-and-plugins-discord-gaming.jpg" alt="Server information Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">Discord Community Discussions</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1 bg-gray-900 hover:bg-gray-800 rounded">
                    <a target="_blank" href="https://wiki.xileretro.net/index.php?title=Donation">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="https://www.teahub.io/photos/full/28-281786_ragnarok-online-ragnarok-online-wallpapers-1920.jpg" alt="Server information Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">Donation<br> Help &<br> Rewards</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1 bg-gray-900 hover:bg-gray-800 rounded">
                    <a target="_blank" href="https://wiki.xileretro.net/index.php?title=MVP">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="https://i.ytimg.com/vi/tEz-SHcyP1Y/maxresdefault.jpg" alt="Server information Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">MVP <br> Ranking <br>  System</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1 bg-gray-900 hover:bg-gray-800 rounded">
                    <a target="_blank" href="https://wiki.xileretro.net/index.php?title=MVP">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="https://e0.pxfuel.com/wallpapers/186/786/desktop-wallpaper-sword-art-online-sword-art-online-sword-art-sword-art-online-pc.jpg" alt="Server information Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">Randomised <br> Weapons <br> Loots</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1 bg-gray-900 hover:bg-gray-800 rounded">
                    <a target="_blank" href="https://wiki.xileretro.net/index.php?title=MVP">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="https://w0.peakpx.com/wallpaper/479/644/HD-wallpaper-i-reading-art-fantasy-reading-butterfly-book-digital-art.jpg" alt="Server information Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">Wikipedia <br> Knowledge <br> Base</h3>
                        </div>
                    </a>
                </div>
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
                        <a class="group relative inline-flex border border-red-600 focus:outline-none mt-6" href="https://wiki.xileretro.net/index.php?title=Donation">
                            <span class="w-full inline-flex items-center justify-center self-stretch px-4 py-2 text-sm text-red-600 text-center font-bold uppercase bg-white ring-1 ring-red-600 ring-offset-1 transform transition-transform group-hover:-translate-y-1 group-hover:-translate-x-1 group-focus:-translate-y-1 group-focus:-translate-x-1">
                                Donate Now
                            </span>
                        </a>
                    </div>
                    <div>
                        <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                        <h3 class="mt-5 text-xl font-bold">Zeny Purchase</h3>
                        <p class="mt-4 text-gray-700 text-sm leading-relaxed">Don't want to ever donate? Well you do not have too just spend some time in game, get to know others and you will have enough zeny to purchase ubers in no time.</p>
                        <a class="group relative inline-flex border border-red-600 focus:outline-none mt-6" href="https://wiki.xileretro.net/index.php?title=Donation">
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

    <section id="important-links" class="bg-black mx-auto px-5 pt-16 pb-24 md:pt-24 lg:pt-32">
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            <div class="">
                <h2 class="text-4xl font-bold md:text-4xl text-gray-100">Uber Store</h2>
                <!-- <p class="mt-6 text-gray-700 leading-relaxed">We pride ourselves on the ability to offer a server that you can compete and join without the need to ever spend real money, to achieve this we offer a dynamic zeny based system to determinate the value of an uber in game which you can then use to purchase donation items. This gives zeny more value and keeps it as main currency while allowing those who want to donate still retain the rewards to support the server.</p> -->
                <p class="mt-6 text-gray-300 leading-relaxed">Your ubers let you get some of the most powerful items in game, ubers can be purchased in game with zeny or by donation, here is a small preview of what is to offer, click to view our wiki for extensive catalogue of items.</p>
                <p class="mt-6 text-gray-200 font-semibold leading-relaxed">Find the Uber Store <span class="text-gray-100">@warp payon 142 224</span></p>
            </div>
            <div class="">
                <ul class="mt-10 relative grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach (config('donation.items') as $item)
                <x-donation-item name="{{ $item['name'] }}" :image="$item['image']" cost="{{ $item['cost'] }}" :set="true">
                    Helm of the fallen Scarlet Angel. <br>DEX +4, STR +4, VIT +8, MDEF +9
                </x-donation-item>
            @endforeach
        </ul>
            </div>
        </div>

    </section>

    <section id="prontera-castles" class="bg-black hidden lg:block relative overflow-hidden py-16 md:pt-32">
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            <div class="container mx-auto flex rounded">
            <h2 class="text-5xl self-center my-0 font-bold mr-16 prose text-gray-100">Prontera<br><span class="font-normal text-lg text-amber-500">Castle Holders</span></h2>
            <div class="py-16 grid grid-cols-5 gap-2 w-full">
                @foreach ($prontera_castles as $castle)
                 @if ($castle->guild->name != config("castles.staff.guildname"))
                    <div class="prose col-span-1 px-6 py-4 text-gray-100 rounded align-center items-center flex flex-col bg-gray-900 hover:bg-gray-700 hover:shadow">
                        @if($castle->guild->hasEmblem())
                            <div class="w-16 h-16 m-0 mb-4 rounded-lg shadow bg-cover bg-gray-100" style="background: url('{{ url($castle->guild->emblem) }}')"></div>
                        @else
                            <img class="h-16 w-16 m-0 mb-4" src="/assets/emblems/empty.bmp"/>
                        @endif
                        <h3 class="truncate ... text-gray-900 bg-amber-500 px-3 rounded font-bold my-0 mb-1">{{ $castle->name }}</h3>
                        <p class="truncate ... mb-1 mt-0">By <span class="text-amber-500">{{ $castle->guild->name }}</span></p>
                        <p class="truncate ... mb-1 mt-0">Leader <span class="text-amber-500">{{ $castle->guild->master }}</span></p>
                        <p class="truncate ... mb-1 mt-0"><span class="text-amber-500">{{ $castle->guild->members->count() }}</span> Members</p>
                    </div>
                 @endif
                @endforeach
            </div>
            </div>
        </div>
    </section>


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

    {{-- <section id="read-the-rules" class="bg-black relative overflow-hidden py-16 md:pt-32 hidden md:block">
        <div class="max-w-screen-xl w-full mx-auto px-5 container mx-auto text-left md:text-center mb-20">
            <h1 class="mb-8 tracking-widest important-title text-blue-500" style="font-size: 3em"><a target="_blank" class="no-underline text-amber-500 hover:text-amber-300 font-bold" href="http://wiki.xileretro.net/index.php?title=Server_Rules">READ THE RULES</a></h1>
            <p><a href="https://discord.gg/hp7CS6k" class="hover:underline">Unfairly banned? Create an appeal</a></p>
        </div>
    </section> --}}

    <section id="read-the-rules" class="bg-black relative overflow-hidden py-16 md:pt-32 hidden md:block">
        <div class="max-w-screen-xl w-full mx-auto px-5 container mx-auto text-left md:text-center mb-20">
            <h1 class="mb-8 tracking-widest important-title text-blue-500" style="font-size: 2.5em"><a target="_blank" class="no-underline text-amber-500 hover:text-amber-300 font-bold" href="http://wiki.xileretro.net/index.php?title=Server_Rules"><span class="text-gray-100">XileRO PK</span> | Third Jobs</a></h1>
            <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5 mt-20">
                <div class="grid grid-cols-3 gap-12 text-gray-100 text-center mt-8">
                    <div class="bg-gray-800 bg-opacity-90 rounded text-gray-100 hover:bg-amber-500 hover:text-gray-900">
                        <a href="https://drive.google.com/drive/folders/1EGeKownNt1cYne1e173OshhbYj31-mh-?usp=sharing">
                            <img class="object-cover w-full rounded h-44" style="margin:0" src="https://wallpapercg.com/media/ts_2x/11480.webp" alt="Server information Image">
                            <div class="p-4">
                                XileRO PK Download
                            </div>
                        </a>
                    </div>
                    <div class="bg-gray-800 bg-opacity-90 rounded text-gray-100 hover:bg-amber-500 hover:text-gray-900">
                        <a href="https://www.facebook.com/groups/670800967076806/">
                            <img class="object-cover w-full rounded h-44" style="margin:0" src="https://wallpapers.com/images/hd/sword-art-online-wallpaper-javjk4u0ar7tbyeu.jpg" alt="Server information Image">
                            <div class="p-4">
                                XileRO PK Facebook
                            </div>
                        </a>
                    </div>
                    <div class="bg-gray-800 bg-opacity-90 rounded text-gray-100 hover:bg-amber-500 hover:text-gray-900">
                        <a href="https://discord.com/invite/cFd4FZupDV">
                            <img class="object-cover w-full rounded h-44" style="margin:0" src="https://wallpapers.com/images/hd/discord-logo-geometric-art-5barh6w9jxj5mhzw.jpg" alt="Server information Image">
                            <div class="p-4">
                                XileRO PK Discord
                            </div>
                        </a>
                    </div>
                </div>
                {{-- <div class="grid grid-cols-4 gap-12 mt-14">
                    <div class="col-span-4 md:col-span-2 lg:col-span-1 bg-gray-900 hover:bg-gray-800 rounded">
                        <a target="_blank" href="https://wiki.xileretro.net/index.php?title=Server_Information">
                            <div class="p-6 rounded-md hover:shadow-lg prose">
                                <div class="mb-6 border border-gray-200 rounded">
                                    <img class="object-cover w-full rounded h-44" style="margin:0" src="https://swall.teahub.io/photos/small/99-999290_ragnarok-online-valkyrie.jpg" alt="Server information Image">
                                </div>
                                <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">Server<br> Information & Features</h3>
                            </div>
                        </a>
                    </div>
                </div> --}}
            </div>
        </div>
    </section>

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

	</section>

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
                    <a target="_blank" href="https://wiki.xileretro.net/index.php?title=Server_Information">
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

@endsection
