@extends('layouts.app')

@section('content')
    @if (session('status'))
        <div class="px-3 py-4 mb-4 text-sm text-green-700 bg-green-100 border border-t-8 border-green-600 rounded" role="alert">
            {{ session('status') }}
        </div>
    @endif

        {{ auth()->user() }}

    <section class="shadow landing">
        <div class="container grid grid-cols-5 gap-4 mx-auto">
            <div class="col-span-3 pt-20 pb-4 pr-6">
                <div class="mr-36">
                    <h1 class="mb-6" style="font-size:2.8em;">XileRetro <br><small class="font-normal">A Ragnarok Online Private Server</small></h1>
                    <p>
                        Welcome to XileRetro, we are a team of developers with dedication
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
                        <button class="flex items-center text-left btn btn-primary">
                            <svg class="w-4 h-4 mr-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg>
                            <span>Create a Ragnarok Account</span>
                        </button>
                        <button class="flex items-center mt-4 text-left btn btn-primary">
                            <svg class="w-4 h-4 mr-2 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg>
                            <span>Download Zip from Mediafire (2GB)</span>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-4 text-5xl social-buttons">
                    <a href="">
                        <i class="fas fa-angle-double-down"></i>
                    </a>
                    <div class="flex flex-row">
                        <a href="">
                            <div class="mr-6 socal-facebook"><i class="fab fa-facebook"></i></div>
                        </a>
                        <a href="">
                            <div class="socal-discord"><i class="fab fa-discord"></i></div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="grid col-span-2">
                <div class="bg-no-repeat bg-cover feature-bg-img">
                    <div style="margin-left:-13%">
                        <img src="images/castle/castle_1.png" alt="Landing image" class="flex items-center justify-center rounded-full w-36 h-36">
                        <img src="images/castle/castle_2.png" alt="Landing image" class="flex items-center justify-center rounded-full w-36 h-36">
                        <img src="images/castle/castle_3.png" alt="Landing image" class="flex items-center justify-center rounded-full w-36 h-36">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="important-links" class="container py-10 mx-auto">
        <h2>Important Links</h2>
        <div class="grid grid-cols-4 col-gap-8">
            <div class="col-span-1">
                <a href="https://wiki.xileretro.net/">
                    <div class="p-4 rounded-md hover:shadow-lg">
                        <div class="mb-6 border border-gray-200 rounded">
                            <img class="object-cover w-full rounded h-44" style="margin:0" src="https://swall.teahub.io/photos/small/99-999290_ragnarok-online-valkyrie.jpg" alt="Server information Image">
                        </div>
                        <h3 style="font-size: 1.5em" class="mt-8 half-border">Server<br> Information &<br>Wiki</h3>
                    </div>
                </a>
            </div>
            <div class="col-span-1">
                <a href="https://wiki.xileretro.net/index.php?title=Newbie_Center#Starter_Package_.5BShow.2FHide.5D">
                    <div class="p-4 rounded-md hover:shadow-lg">
                        <div class="mb-6 border border-gray-200 rounded">
                            <img class="object-cover w-full rounded h-44" style="margin:0" src="https://swall.teahub.io/photos/small/99-998493_ragnarok-online-artwork-anime-games-mmorpg-ragnarok-online.jpg" alt="Server information Image">
                        </div>
                        <h3 style="font-size: 1.5em" class="mt-8 half-border">Starter<br>Packages & <br>Guides</h3>
                    </div>
                </a>
            </div>
            <div class="col-span-1">
                <a href="https://wiki.xileretro.net/index.php?title=Leveling_Spots">
                    <div class="p-4 rounded-md hover:shadow-lg">
                        <div class="mb-6 border border-gray-200 rounded">
                            <img class="object-cover w-full rounded h-44" style="margin:0" src="https://swall.teahub.io/photos/small/99-994510_photo-wallpaper-forest-flower-grass-elf-art-girl.jpg" alt="Server information Image">
                        </div>
                        <h3 style="font-size: 1.5em" class="mt-8 half-border">Leveling<br>Areas & <br>Progression</h3>
                    </div>
                </a>
            </div>
            <div class="col-span-1">
                <a href="https://wiki.xileretro.net/index.php?title=Donation">
                    <div class="p-4 rounded-md hover:shadow-lg">
                        <div class="mb-6 border border-gray-200 rounded">
                            <img class="object-cover w-full rounded h-44" style="margin:0" src="https://www.teahub.io/photos/full/28-281786_ragnarok-online-ragnarok-online-wallpapers-1920.jpg" alt="Server information Image">
                        </div>
                        <h3 style="font-size: 1.5em" class="mt-8 half-border">Donation<br> Help &<br> Rewards</h3>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <div class="container mx-auto">
        <hr class="default">
    </div>

    <section id="steps2play" class="container pt-5 pb-20 mx-auto" style="">
        <div class="grid grid-cols-2">
            <div class="col-span-1">
                @guest
                <h2 class="mt-0">Let's get you in game!</h2>
                <h4>Register your Ragnarok Account</h4>
                <form class="w-full max-w-lg" method="POST" action="/register">
                    @csrf
                    <div class="flex flex-wrap mb-2 -mx-3">
                        <div class="w-full px-3">
                            <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="grid-username">
                                Username
                            </label>
                            <input name="username" class="block w-full px-4 py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border border-gray-200 rounded appearance-none focus:outline-none focus:bg-white focus:border-gray-500 @error('username') border-red-500 @enderror" id="grid-username" value="{{ old('username') }}" type="text" placeholder="username" required autocomplete="name" autofocus>
                            @error('username')
                                <p class="mt-4 text-xs italic text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex flex-wrap mb-2 -mx-3">
                        <div class="w-full px-3">
                            <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="grid-email">
                                Email Address
                            </label>
                            <input name="email" class="block w-full px-4 py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border border-gray-200 rounded appearance-none focus:outline-none focus:bg-white focus:border-gray-500 @error('email') border-red-500 @enderror" id="grid-email" value="{{ old('email') }}" type="email" placeholder="account@xileretro.net" required autocomplete="email">
                            @error('email')
                                <p class="mt-4 text-xs italic text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex flex-wrap mb-2 -mx-3">
                        <div class="w-full px-3">
                            <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="grid-password">
                                Password
                            </label>
                            <input name="password" class="block w-full px-4 py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border border-gray-200 rounded appearance-none focus:outline-none focus:bg-white focus:border-gray-500 @error('password') border-red-500 @enderror" id="grid-password" type="password" placeholder="******************" required autocomplete="new-password">
                            @error('password')
                                <p class="mt-4 text-xs italic text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex flex-wrap mb-2 -mx-3">
                        <div class="w-full px-3">
                            <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="grid-password-confirm">
                                Confirm Password
                            </label>
                            <input name="password_confirmation" class="block w-full px-4 py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border border-gray-200 rounded appearance-none focus:outline-none focus:bg-white focus:border-gray-500" id="grid-password-confirm" type="password" placeholder="******************" required autocomplete="new-password">
                        </div>
                    </div>
					<div class="flex justify-start mt-6">
                        <button class="col-span-1 btn btn-primary">
                            <span>Create New Account</span>
                        </button>
                    </div>
                </form>
                @else
                <div class="pr-28">
                    <h2 class="mt-0">You are ready to log in game!</h4>
                    <p>Open up your client and login with the username <b>{{ auth()->user()->userid }}</b> and the password you used to create the account!</p>
                    <hr>
                    <h2>Get a Headstart</h4>
                    <p>If you are new to XileRetro or would like a refresher, we highly recommend checking out the <a href="http://wiki.xileretro.net/index.php?title=Newbie_Center" target="_blank">Newbie Center Guide</a> for an awesome head start!</p>
                    <hr>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        {{ csrf_field() }}
                        <button class="btn btn-secondary" action="submit">Logout from Website</button>
                    </form>

                </div>
                @endguest
            </div>
            <div class="col-span-1">
                <h3>Grab a download.</h3>
				<h4>Download and install with Lite Installer.</h4>
				<div class="grid grid-cols-5">
					<div class="col-span-1">
						<i class="fas fa-file-archive step2-icon"></i>
					</div>
					<div class="col-span-4">
                        @foreach(config('downloads.lite') as $item)
                            <a class="no-underline" href="{{ $item['link'] }}" target="_blank">
                                <button class="flex items-center w-full mb-4 text-left btn {{ $item['bttn'] }}">
                                    {{ $item['name'] }}
                                </button>
                            </a>
                        @endforeach
					</div>
				</div>

				<h4>Download and install with Full Installer.</h3>
				<div class="grid grid-cols-5">
					<div class="col-span-1">
						<i class="fas fa-compact-disc step2-icon"></i>
					</div>
					<div class="col-span-4">
                        @foreach(config('downloads.full') as $item)
                            <a class="no-underline" href="{{ $item['link'] }}" target="_blank">
                                <button class="flex items-center w-full mb-4 text-left btn {{ $item['bttn'] }}">
                                    {{ $item['name'] }}
                                </button>
                            </a>
                        @endforeach
					</div>
				</div>

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

@endsection
