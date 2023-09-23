<x-app-layout>

    <section class="shadow bg-clash-bg bg-right py-20 pt-80">
        <div class="section-div text-gray-100">
            <span class="text-[140px] center-letter">X</span>
            <span class="text-[140px] center-letter">I</span>
            <span class="text-[140px] center-letter">L</span>
            <span class="text-[140px] center-letter">E</span>
            <span class="text-[140px] center-letter">R</span>
            <span class="text-[140px] center-letter">O</span>
        </div>
    </section>


    <section id="read-the-rules" class="bg-clash-bg relative rounded-lg overflow-hidden py-24 md:pt-32 hidden md:block">
        <div class="z-0 absolute effect-light-blue-bang top-[20px] right-[140px]"></div>
        <div class="z-0 absolute effect-light-yellow-bang top-[20px] right-[180px]"></div>
        <div class="z-10 relative block-home max-w-screen-xl w-full mx-auto flex justify-between container md:text-center mb-0 bg-gray-900 p-4 py-8 rounded to-transparent">
            <div class="no-underline text-gray-100 hover:text-amber-300 font-bold text-2xl cursor-pointer">Connect with Low Ping, Globally</div>
            <div class="no-underline text-amber-500 hover:text-amber-300 font-bold text-2xl cursor-pointer" href=""><span class="text-gray-100">XileRO</span> | HyperDrive™  [<span class="text-gray-100 cursor-text">{{ config('xilero.hyperdrive.ip_address') }}</span>]</div>
        </div>
    </section>

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

    @if(auth()->check())
        <livewire:my-account-details/>
    @endif


    <section id="steps2play" class="bg-clash-bg relative overflow-hidden py-12 px-24">
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            <livewire:register lazy />
        </div>
    </section>

    <div class="line"></div>
    <div class="hidden md:block">
        <x-latest-post-preview lazy/>
    </div>
    <div class="line"></div>

    {{-- <section id="mvprankingladder" class="container mx-auto grid">
        <h2>MVP Ladder</h2>
        @foreach (App\Ragnarok\MvpLadderRank::orderByDesc('day_kills')->limit(3)->get() as $rank)
            <div class="cols-span-1">
                <p>Player {{ $rank->name }}</p>
                <p>{{ $rank->day_kills }} MVP Kills Today!</p>
            </div>
        @endforeach
    </section> --}}

    <section id="important-links" class="relative overflow-hidden py-8 bg-clash-bg">
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            <h2>Getting Started</h2>
            <p class="mt-6 text-gray-300 leading-relaxed">Welcome to the world of Xilero, where unique adventures await! If you're new to our server or looking to enhance your gameplay experience, you've come to the right place. Our Getting Started guides are crafted to help players of all levels navigate the distinct features and mechanics that set Xilero apart.</p>
            <div class="grid grid-cols-4 gap-12 mt-14">
                <div class="col-span-4 md:col-span-2 lg:col-span-1 block-home">
                    <a title="Learn more about Server Information & Features on XileRetro Wiki" target="_blank" rel="noopener" href="https://wiki.xileretro.net/index.php?title=Server_Information">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="{{ asset('assets/getting-started/server-information.jpeg') }}" alt="Server Information & Features">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">Server<br> Information & Features</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1 block-home hover:shadow-md hover:shadow-violet-500">
                    <a title="Essential Starter Packages & Guides for New Players" target="_blank" rel="noopener" href="https://wiki.xileretro.net/index.php?title=Newbie_Center">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="{{ asset('assets/getting-started/starter-packages.jpeg') }}" alt="Starter Packages & Guids Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">Starter<br>Packages & <br>Guides</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1 block-home hover:shadow-md hover:shadow-violet-500">
                    <a title="Discover Best Leveling Areas & Progression Tips on XileRetro Wiki" target="_blank" rel="noopener" href="https://wiki.xileretro.net/index.php?title=Leveling_Spots">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="{{ asset('assets/getting-started/leveling-areas.jpeg') }}" alt="Leveling Areas & Progression Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">Leveling<br>Areas & <br>Progression</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1 block-home hover:shadow-md hover:shadow-violet-500">
                    <a title="Join our Discord Community Discussions" target="_blank" rel="noopener" href="https://discord.gg/hp7CS6k">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="{{ asset('assets/getting-started/discord-community.jpeg') }}" alt="Discord Community Discussions Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">Discord Community Discussions</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1 block-home hover:shadow-md hover:shadow-violet-500">
                    <a title="Learn about the Donation Help & Rewards on XileRetro Wiki" target="_blank" rel="noopener" href="https://wiki.xileretro.net/index.php?title=Donation">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="{{ asset('assets/getting-started/donation-help.jpeg') }}" alt="Donation Help & Rewards">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">Donation<br> Help &<br> Rewards</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1 block-home hover:shadow-md hover:shadow-violet-500">
                    <a title="Explore the MVP Ranking System on XileRetro Wiki" target="_blank" rel="noopener" href="https://wiki.xileretro.net/index.php?title=MVP">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="{{ asset('assets/getting-started/mvp-ranking.jpeg') }}" alt="MVP Ranking System Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">MVP <br> Ranking <br>  System</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1 block-home hover:shadow-md hover:shadow-violet-500">
                    <a title="Discover Randomised Weapons Loots on XileRetro Wiki" target="_blank" rel="noopener" href="https://wiki.xileretro.net/index.php?title=MVP">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="{{ asset('assets/getting-started/randomized-weapon-loots.jpeg') }}" alt="Randomised Weapon Loot Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-2 half-border font-normal text-gray-100">Randomised <br> Weapons <br> Loots</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1 block-home hover:shadow-md hover:shadow-violet-500">
                    <a title="Explore the Wikipedia Knowledge Base on XileRetro Wiki" target="_blank" rel="noopener" href="https://wiki.xileretro.net/index.php?title=MVP">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="{{ asset('assets/getting-started/wikipedia-knowledge.jpeg') }}" alt="Wikipedia Knowledge Base">
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

    <div class="line"></div>

    <section id="uber-store" class="bg-clash-bg mx-auto py-5 pt-8 pb-24">
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            <div class="">
                <div class="grid grid-cols-2">
                    <h2 class="mb-0">Uber Store</h2>
                    <h2 class="col-span-2 md:col-span-1 text-3xl text-left md:text-right font-bold mb-0 text-amber-500">Live Price: {{ cache()->remember('index.live_uber', now()->addMinutes(10), fn() => number_format($server_zeny->total_uber_cost) ?? 0) }} Zeny</h2>
                </div>{{--                <h3 class="text-white text-2xl mt-4">Current Uber Cost: 1,000000 zeny</h3>--}}
                <!-- <p class="mt-6 text-gray-700 leading-relaxed">We pride ourselves on the ability to offer a server that you can compete and join without the need to ever spend real money, to achieve this we offer a dynamic zeny based system to determinate the value of an uber in game which you can then use to purchase donation items. This gives zeny more value and keeps it as main currency while allowing those who want to donate still retain the rewards to support the server.</p> -->
                <p class="mt-6 text-gray-300 leading-relaxed">Your ubers let you get some of the most powerful items in game, ubers can be purchased in game with zeny or by donation, here is a small preview of what is to offer, click to view our wiki for extensive catalogue of items. <span class="text-amber-500">@warp payon 142 224</span></p>
            </div>
            <div class="">
                <ul class="mt-10 relative grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach (config('donation.items') as $item)
                        <li class="block-home p-6">
                            <a id="{{ Str::slug($item['name']) }}" title="Uber Shop Item {{ $item['name'] }}" aria-label="Uber Shop Item {{ $item['name'] }}" href="https://wiki.xileretro.net/index.php?title=Donation" class="flex">
                                <div class="relative shrink-0 bg-breeze flex items-center justify-center rounded-lg overflow-hidden" style="height:100px; width:75px;">
                                    {{-- <span class="absolute w-full h-full inset-0 bg-gradient-to-b from-[rgba(255,255,255,.2)] to-[rgba(255,255,255,0)]"></span> --}}
                                    <img src="/images/donations/{{ $item['image'] }}" alt="{{ $item['name'] }} Item" class="relative" width="75" height="100">
                                </div>
                                <div class="ml-4 leading-5">
                                    <div class="text-gray-100">{{ $item['name'] }}</div>
                                    <div class="mt-1 text-sm text-gray-300">{{ $item['description'] }} <br> {{ $item['stats'] }}</div>
                                    <div class="mt-1 text-sm text-amber-500 font-bold">{{ $item['cost'] }} Ubers</div>
                                    @if(isset($set))
                                        <div class="mt-1 text-sm text-amber-200 font-bold">Click to view Item Set Bonus</div>
                                    @endif
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </section>

    <div class="line"></div>

    <section id="important-links" class="bg-clash-bg relative overflow-hidden py-8 pb-32">
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            <h2>Server Vending</h2>
            <p class="mt-6 text-gray-300 leading-relaxed">Currently in beta, live vending data allows you to search for item IDs to check availability, price, and vendor location. Future feature updates will enhance support.</p>
            <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5 mt-10">
                <livewire:server-vendings lazy/>
            </div>
        </div>
    </section>

    <div class="line"></div>

    <section id="prontera-castles" class="bg-clash-bg hidden lg:block relative overflow-hidden py-8">
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            <div class="container mx-auto rounded">
                <div class="">
                    <div class="text-gray-100 flex justify-between">
                        <h2>Woe Times</h2>
                        <a class="text-amber-200 text-lg hover:text-underline" href="{{ route('woe') }}">View More</a>
                    </div>
                    <p class="mt-6 text-gray-300 leading-relaxed mb-8">Prepare for battle and mark your calendars! The War of Emperium on Xilero takes place across various timezones, ensuring that warriors from all corners of the world can join the fight. Find the schedule that fits your timezone below and rally your guild for the epic clashes in the specified castles.</p>
                </div>
                <div class="grid grid-cols-{{$castles->count()}} gap-5">
                    @foreach ($castles as $castle)
                        <div class="mb-4 block-home bg-opacity-70 rounded p-5">
                            <h3 class="text-amber-200 text-2xl mb-8 half-border">{{ $castle->name }}</h3>
                            @foreach(config('castles.timezones') as $timezone)
                                <div class="text-white mb-8 flex rounded">
                                    <div class="mr-3">
                                        @if($castle->guild->hasEmblem())
                                            <div class="w-8 h-8 m-0 shadow" style="background: url('{{ $castle->guild->emblem }}'); background-size:contain;"></div>
                                        @else
                                            <img class="h-8 w-8 m-0" src="/assets/emblems/empty.bmp"/>
                                        @endif
                                    </div>
                                    <div class="">
                                        <h4 class="text-white font-bold mb-1">{{ $timezone }}</h4>
                                        @foreach(config("castles.prontera.{$castle->name}.day") as $day)
                                                <?php
                                                $date = new DateTime();
                                                $date->modify("next {$day}");
                                                $time = DateTime::createFromFormat("H:i", config("castles.prontera.{$castle->name}.time"));
                                                $date->setTime($time->format('H'), $time->format('i'))->modify(config('castles.modifier'));
                                                ?>
                                            <p class="mt-1 text-gray-400">{{ $date->setTimezone(new DateTimeZone($timezone))->format("l, H:i A") }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
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


</x-app-layout>
