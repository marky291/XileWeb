<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ mix('assets/core.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" rel="stylesheet">
</head>
<body class="h-screen antialiased leading-none">

    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v8.0&appId=661838800646818&autoLogAppEvents=1" nonce="ezF5mpW9"></script>

    <div id="app" class="prose max-w-none">

        <nav style="height:60px; background-color: #6e3755">
            <a class="text-white" href="/">Home</a>
        </nav>

        @yield('content')

        <footer id="footer" class="shadow-inner">
            <div class="px-20 pt-5 pb-20 content">
                <img src="/images/logo.png" alt="XileRetro Logo" class="w-1/3">
                <div class="grid grid-cols-2">
                    <div class="grid grid-cols-2 col-span-1 col-gap-6">
                        <ul>
                            <li><a class="text-white no-underline hover:underline" href="#top">Home</a></li>
                            <li><a class="text-white no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Server_Rules">Rulebook</a></li>
                            <li><a class="text-white no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Server_Information#Staff">The Staff</a></li>
                            <li><a class="text-white no-underline hover:underline" href="#download">Download</a></li>
                            <li><a class="text-white no-underline hover:underline" href="#register">Register</a></li>
                        </ul>
                        <ul>
                            <li><a class="text-white no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Server_Information">Server Features</a></li>
                            <li><a class="text-white no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=General_Customs">Modified Official</a></li>
                            <li><a class="text-white no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Updates_Changelog">Updates & Changelog</a></li>
                            <li><a class="text-white no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Newbie_Center">Newbie Center</a></li>
                            <li><a class="text-white no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Events">Events</a></li>
                        </ul>
                    </div>
                    <div class="col-span-1"></div>
                </div>
            </div>
            <div class="grid grid-cols-3 px-20 pt-8 pb-10 shadow-inner copyright">
                <div class="flex items-center col-span-2">
                    <i class="mr-4 text-5xl text-gray-300 far fa-heart"></i>
                    <p class="text-gray-300">A thank you to all our players who support and show commitment<br>to making a server that's great for everyone.</p>
                </div>
                <div class="flex items-end justify-end col-span-1 text-right">
                    <p class="text-gray-300">Website design and coding by Mark Hester<br>(Co-Owner of XileRetro)</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
