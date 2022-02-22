@extends('layouts.app')

@section('content')
    @if (session('status'))
        <div class="px-3 py-4 mb-4 text-sm text-green-700 bg-green-100 border border-t-8 border-green-600 rounded" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <section class="shadow landing">
        <div class="container grid px-3 sm:px-0 grid-cols-5 gap-4 mx-auto">
            <div class="col-span-5 lg:col-span-3 pt-20 pb-4 pr-6">
                <div class="xl:mr-36 prose">
                    <h1 class="mb-6" style="font-size:2.8em;">XileRetro <br><small class="font-normal">A Ragnarok Online Private Server</small></h1>
                    <p>
                        Welcome, we are a team of developers with dedication
                        and love for the mechanics that drive Xile and the skills behind it.
                        Our server is an official implementation of the old eAthena architecture
                        into modern rAthena code where we can provide support for future mechanics
                        and features that were previously incapable.
                    </p>
                    <p>
                        We secured the platform
                        using the latest Gepard 3.0 security systems that helps prevents hacks
                        and macros to ultimately provide a solid foundation for future growth
                        internally and externally. We are not just here for the old players,
                        we are also here for the new~ So log in and pawn some noobs!!
                    </p>
                    <div class="flex flex-col my-10 quick-links">
                        <a href="#steps2play" class="w-full no-underline">
                            <button id="hero-registration" class="flex items-center w-full text-left btn btn-primary bg-rose-800 hover:bg-rose-900">
                                <!-- <svg class="w-4 h-4 mr-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg> -->
                                <span>Create a Ragnarok Account</span>
                            </button>
                        </a>
                        <a href="{{ config('downloads.full')[0]['link'] }}" target="_blank" class="w-full no-underline hidden lg:block">
                            <button id="hero-download-full-client" class="flex items-center w-full mt-4 text-left btn btn-primary bg-white border border-rose-900 text-rose-900 border-rose-900">
                                <!-- <svg class="w-4 h-4 mr-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg> -->
                                <span>{{ config('downloads.full')[array_key_first(config('downloads.full'))]['name'] }}</span>
                            </button>
                        </a>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-4 text-5xl social-buttons">
                    <a href="#important-links" class="text-gray-700 hover:text-cool-gray-900">
                        <i class="fas fa-angle-double-down"></i>
                    </a>
                    <div class="flex flex-row">
                        <a href="https://www.facebook.com/xileretro" class="text-blue-500 hover:text-blue-800">
                            <div class="mr-6 socal-facebook text-blue-500 hover:text-blue-800"><i class="fab fa-facebook"></i></div>
                        </a>
                        <a href="https://discord.gg/hp7CS6k" class="text-indigo-500 hover:text-indigo-800">
                            <div class="socal-discord text-indigo-500 hover:text-indigo-800"><i class="fab fa-discord"></i></div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="lg:grid col-span-2 hidden">
                <div class="bg-no-repeat bg-cover feature-bg-img">
                    <div style="margin-left:-13%" class="hidden xl:block relative">
                        <img src="images/castle/castle_1.png" alt="Landing image" class="absolute top-[60px] flex items-center justify-center rounded-full w-36 h-36">
                        <img src="images/castle/castle_2.png" alt="Landing image" class="absolute top-[240px] flex items-center justify-center rounded-full w-36 h-36">
                        <img src="images/castle/castle_3.png" alt="Landing image" class="absolute top-[420px] flex items-center justify-center rounded-full w-36 h-36">
                    </div>
                    <!-- <div style="margin-left:-19%" class="relative">
                        <img alt="{{ $prontera_castles[0]->guild->name }}" class="w-12 h-12 absolute rounded-md right-12 top-[0px] translate-y-12 pointer-events-none md:left-[12%]" src="{{ $prontera_castles[0]->guild->hasEmblem() ? url($prontera_castles[0]->guild->emblem) : '/assets/emblems/empty.bmp' }}">
                        <img alt="{{ $prontera_castles[1]->guild->name }}" class="w-12 h-12 absolute rounded-md right-12 top-[90px] translate-y-12 pointer-events-none md:left-[12%]" src="{{ $prontera_castles[1]->guild->hasEmblem() ? url($prontera_castles[1]->guild->emblem) : '/assets/emblems/empty.bmp' }}">
                        <img alt="{{ $prontera_castles[2]->guild->name }}" class="w-12 h-12 absolute rounded-md right-12 top-[180px] translate-y-12 pointer-events-none md:left-[12%]" src="{{ $prontera_castles[2]->guild->hasEmblem() ? url($prontera_castles[2]->guild->emblem) : '/assets/emblems/empty.bmp' }}">
                        <img alt="{{ $prontera_castles[3]->guild->name }}" class="w-12 h-12 absolute rounded-md right-12 top-[270px] translate-y-12 pointer-events-none md:left-[12%]" src="{{ $prontera_castles[3]->guild->hasEmblem() ? url($prontera_castles[3]->guild->emblem) : '/assets/emblems/empty.bmp' }}">
                        <img alt="{{ $prontera_castles[4]->guild->name }}" class="w-12 h-12 absolute rounded-md right-12 top-[360px] translate-y-12 pointer-events-none md:left-[12%]" src="{{ $prontera_castles[4]->guild->hasEmblem() ? url($prontera_castles[4]->guild->emblem) : '/assets/emblems/empty.bmp' }}">
                    </div> -->
                </div>
            </div>
        </div>
    </section>

    <section id="steps2play" class="relative overflow-hidden py-16 md:pt-24 lg:pt-64">
        <div class="max-w-screen-xl w-full mx-auto px-5">
            <livewire:register/>
        </div>
    </section>

    <!-- <section id="mvprankingladder" class="container mx-auto grid">
        <h2>MVP Ladder</h2>
        @foreach (App\Ragnarok\MvpLadderRank::orderByDesc('day_kills')->limit(3)->get() as $rank)
            <div class="cols-span-1">
                <p>Player {{ $rank->name }}</p>
                <p>{{ $rank->day_kills }} MVP Kills Today!</p>
            </div>
        @endforeach 
    </section> -->

    <section id="important-links" class="relative overflow-hidden py-16 md:pt-48">
        <div class="max-w-screen-xl w-full mx-auto px-5">
            <h2 class="text-2xl font-semibold mb-5">Getting Started</h2>
            <div class="grid grid-cols-4 col-gap-8">
                <div class="col-span-4 md:col-span-2 lg:col-span-1">
                    <a href="https://wiki.xileretro.net/">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="https://swall.teahub.io/photos/small/99-999290_ragnarok-online-valkyrie.jpg" alt="Server information Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-8 half-border font-normal">Server<br> Information &<br>Wiki</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1">
                    <a href="https://wiki.xileretro.net/index.php?title=Newbie_Center#Starter_Package_.5BShow.2FHide.5D">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="https://swall.teahub.io/photos/small/99-998493_ragnarok-online-artwork-anime-games-mmorpg-ragnarok-online.jpg" alt="Server information Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-8 half-border font-normal">Starter<br>Packages & <br>Guides</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1">
                    <a href="https://wiki.xileretro.net/index.php?title=Leveling_Spots">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="https://swall.teahub.io/photos/small/99-994510_photo-wallpaper-forest-flower-grass-elf-art-girl.jpg" alt="Server information Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-8 half-border font-normal">Leveling<br>Areas & <br>Progression</h3>
                        </div>
                    </a>
                </div>
                <div class="col-span-4 md:col-span-2 lg:col-span-1">
                    <a href="https://wiki.xileretro.net/index.php?title=Donation">
                        <div class="p-6 rounded-md hover:shadow-lg prose">
                            <div class="mb-6 border border-gray-200 rounded">
                                <img class="object-cover w-full rounded h-44" style="margin:0" src="https://www.teahub.io/photos/full/28-281786_ragnarok-online-ragnarok-online-wallpapers-1920.jpg" alt="Server information Image">
                            </div>
                            <h3 style="font-size: 1.5em" class="mt-8 half-border font-normal">Donation<br> Help &<br> Rewards</h3>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="prontera-castles" class="hidden lg:block relative overflow-hidden py-16 md:pt-48">
        <div class="max-w-screen-xl w-full mx-auto px-5">
            <div class="container mx-auto flex rounded">
            <h2 class="text-5xl self-center my-0 font-bold mr-16 prose">Prontera<br><span class="font-normal text-lg text-rose-900">Castle Holders</span></h2>
            <div class="py-16 grid grid-cols-5 gap-2 w-full">
                @foreach ($prontera_castles as $castle)
                    <div class="prose col-span-1 px-6 py-4 rounded align-center items-center flex flex-col hover:bg-gray-100 hover:shadow">
                        @if($castle->guild->hasEmblem())
                            <div class="w-16 h-16 m-0 mb-4 rounded-lg shadow bg-cover bg-gray-100" style="background: url('{{ url($castle->guild->emblem) }}')"></div>
                        @else
                            <img class="h-16 w-16 m-0 mb-4" src="/assets/emblems/empty.bmp"/>
                        @endif
                        <h3 class="truncate ... font-bold my-0 mb-1">{{ $castle->name }}</h3>
                        <p class="truncate ... mb-1 mt-0">By <span class="text-rose-900">{{ $castle->guild->name }}</span></p>
                        <p class="truncate ... mb-1 mt-0">Leader <span class="text-rose-900">{{ $castle->guild->master }}</span></p>
                        <p class="truncate ... mb-1 mt-0"><span class="text-rose-900">{{ $castle->guild->members->count() }}</span> Members</p>
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
                        <a href="https://discord.gg/hjSSSXX" class="btn bg-gray-500 text-white no-underline">Report android issues on our discord</a>
                    </div>
                </div>
            </div>
        </section>
    --}}

    <section id="read-the-rules" class="relative overflow-hidden py-16 md:pt-48 hidden md:block">
        <div class="max-w-screen-xl w-full mx-auto px-5 container mx-auto text-left md:text-center mb-20">
            <h1 class="mb-8 tracking-widest important-title text-blue-500" style="font-size: 3em"><a target="_blank" class="no-underline text-blue-500 hover:text-blue-900 font-bold" href="http://wiki.xileretro.net/index.php?title=Server_Rules">READ THE RULES</a></h1>
            <p><a href="https://discord.gg/M4nP4rn" class="hover:underline">Unfairly banned? Create an appeal</a></p>
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

@endsection
