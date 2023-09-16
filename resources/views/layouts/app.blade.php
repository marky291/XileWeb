<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>

        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-Q5ECW50F0V"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-Q5ECW50F0V');
        </script>

        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-KTSGRGHJ');</script>
        <!-- End Google Tag Manager -->

        <meta charset="utf-8">

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="@yield('description', 'XileRO is a leading PK server, offering unique mechanics and one-of-a-kind classic style gameplay. Experience XileRetro today and join the immersive world that redefines PK gaming.')">
        <meta name="keywords" content="@yield('keywords', 'Ragnarok, Ragnarok Online, Classic, RO, XileRO, XileRetro, XileRO PK Retro, XileRO PK, PK Server, Woe Server')">
        <meta name="author" content="XileRO, XileRetro">
        <meta name="robots" content="index, follow">

        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

        <title>@yield('title', 'XileRO | The Ultimate Classic Experience')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.cdnfonts.com/css/bonechiller" rel="stylesheet">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
{{--        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" rel="stylesheet">--}}

        <!-- Scripts -->
        @filamentStyles
        @filamentScripts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://kit.fontawesome.com/0e7a67f5fc.js" crossorigin="anonymous"></script>
    </head>
    <body x-data="{
        navIsOpen: false,
        searchIsOpen: false,
        search: '',
    }" class="h-screen antialiased leading-none">

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KTSGRGHJ"
                      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <script src="https://cdn.jsdelivr.net/npm/@widgetbot/crate@3" async defer>
        const crate = new Crate({
            server: '702319926110584943',
            channel: '702319926500655135'
        })
        crate.notify("⭒❃.✮: Welcome to XileRO, Dont forget to join our discord community! :✮.❃⭒")
    </script>

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">


        <div id="app" class="max-w-none">

            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-clash-foot shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="bg-clash-bg">
                {{ $slot }}
            </main>

                <footer id="footer" class="bg-clash-foot shadow-inner">
                    <div class="container px-3 md:px-0 grid grid-cols-2 col-gap-1 pt-12 mx-auto pb-15 content">
                        <div class="col-span-2 lg:col-span-1">
                            {{-- <img src="/images/logo.png" alt="XileRetro Logo" class="w-1/2"> --}}
                            <div class="grid grid-cols-2">
                                <h2 class="text-xl font-bold my-6 text-gray-100">Navigate</h2>
                                <div class="grid grid-cols-2 col-span-2 col-gap-6 prose">
                                    <ul class="m-0">
                                        <li><a class="text-gray-300 no-underline hover:underline" href="{{ url('/') }}" title="XileRetro Home Page">Home</a></li>
                                        <li><a target="_blank" rel="noopener" class="text-gray-300 no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Server_Rules" title="XileRetro Server Rules">Rulebook</a></li>
                                        <li><a target="_blank" rel="noopener" class="text-gray-300 no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Server_Information#Staff" title="Information about XileRetro Staff">The Staff</a></li>
                                        <li><a target="_blank" rel="noopener" class="text-gray-300 no-underline hover:underline" href="#steps2play" title="Download XileRetro">Download</a></li>
                                        <li><a target="_blank" rel="noopener" class="text-gray-300 no-underline hover:underline" href="#steps2play" title="Register for XileRetro">Register</a></li>
                                    </ul>
                                    <ul class="m-0">
                                        <li><a target="_blank" rel="noopener" class="text-gray-300 no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Server_Information" title="XileRetro Server Features">Server Features</a></li>
                                        <li><a target="_blank" rel="noopener" class="text-gray-300 no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=General_Customs" title="Modified official information of XileRetro">Modified Official</a></li>
                                        <li><a target="_blank" rel="noopener" class="text-gray-300 no-underline hover:underline" href="https://wiki.xileretro.net/index.php?title=Category:Release_Notes" title="XileRetro Updates and Release Notes">Updates & Release Notes</a></li>
                                        <li><a target="_blank" rel="noopener" class="text-gray-300 no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Newbie_Center" title="Support Center for XileRetro Newbies">Newbie Center</a></li>
                                        <li><a target="_blank" rel="noopener noreferrer" class="text-gray-300 no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Events" title="XileRetro Events and Activities">Events</a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="flex items-center p-0 mt-8">
                                <p class="my-0 mt-12 ml-0 mr-6 text-5xl text-green-500 mb-5">Server is Online!</p>
                            </div>
                            <p class="mb-20 text-gray-300">Operational ({{ cache()->remember('footer.live-player-count', now()->addMinutes(10), fn() => number_format(App\Ragnarok\Char::query()->online()->count() ?? 0)) }})</p>
                        </div>
                        <div class="col-span-2 lg:col-span-1">
                            <h2 class="text-xl font-bold my-6 text-gray-100">Community Loaders</h2>
                            <div class="grid grid-cols-3 screenshots">
                                <img class="col-span-1 my-0" src="images/loading/loading00.png" title="View XileRetro Wallpaper 1" alt="XileRetro Wallpaper 1">
                                <img class="col-span-1 my-0" src="images/loading/loading06.png" title="View XileRetro Wallpaper 2" alt="XileRetro Wallpaper 2">
                                <img class="col-span-1 my-0" src="images/loading/loading08.png" title="View XileRetro Wallpaper 3" alt="XileRetro Wallpaper 3">
                            </div>
                            <div>
                                <h2 class="text-xl mt-20 font-bold my-6 text-gray-100">Get in Touch</h2>
                                <div class="grid grid-cols-3 col-gap-3">
                                    <a href="https://www.facebook.com/groups/XileRetro" title="XileRetro's Facebook Group" class="no-underline">
                                        <div class="flex items-center col-span-1">
                                            <i class="mr-2 text-4xl text-blue-500 fab fa-facebook-square"></i>
                                            <p class="m-0 text-blue-100">Facebook Page</p>
                                        </div>
                                    </a>
                                    <a href="https://discord.gg/hp7CS6k" title="Join XileRetro on Discord" class="no-underline">
                                        <div class="flex items-center col-span-1">
                                            <i class="mr-2 text-4xl text-indigo-500 fab fa-discord"></i>
                                            <p class="m-0 text-indigo-100">Discord</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container px-3 mt-10 md:mt-0 md:px-0 grid grid-cols-3 pt-8 pb-10 mx-auto copyright">
                        <div class="flex items-center col-span-3 lg:col-span-2 lg:text-left text-gray-300 prose">
                            <i class="mr-4 text-5xl far fa-heart"></i>
                            <p class="mt-0">A thank you to all our players who support and show commitment<br>to making a server that's great for everyone.</p>
                        </div>
                        <div class="hidden lg:flex items-end justify-end col-span-3 lg:col-span-1 text-right prose text-gray-300">
                            <p class="mt-0">Website design and coded by XileRetro<br><span class="text-gray-100">Version {{ config('app.version') }}</span></p>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>
