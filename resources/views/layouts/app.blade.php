<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-167913241-1"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-167913241-1');
    </script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-5RS9B8V');</script>
    <!-- End Google Tag Manager -->

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'XileRO Retro Server | No Third Jobs')</title>

    <!-- Styles -->
    <link href="{{ mix('assets/core.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" rel="stylesheet">

    @livewireStyles
</head>
<body x-data="{
        navIsOpen: false,
        searchIsOpen: false,
        search: '',
    }" class="h-screen antialiased leading-none">

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5RS9B8V"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- Load Facebook SDK for JavaScript -->
      <div id="fb-root"></div>
      <script>
        window.fbAsyncInit = function() {
          FB.init({
            xfbml            : true,
            version          : 'v9.0'
          });
        };

        (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
        fjs.parentNode.insertBefore(js, fjs);
      }(document, 'script', 'facebook-jssdk'));</script>

      <!-- Your Chat Plugin code -->
      <div class="fb-customerchat"
        attribution=setup_tool
        page_id="100309695047046"
  theme_color="#6e3855"
  logged_in_greeting="Hello! 👋 We are here to help, let us know if you have any questions"
  logged_out_greeting="Hello! 👋 We are here to help, let us know if you have any questions!">
      </div>

    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v8.0&appId=661838800646818&autoLogAppEvents=1" nonce="ezF5mpW9"></script>

    <div id="app" class="max-w-none">

      <x-navigation/>


        @yield('content')

        <footer id="footer" class="bg-gray-900 shadow-inner">
            <div class="container px-3 md:px-0 grid grid-cols-2 col-gap-1 pt-12 mx-auto pb-15 content">
                <div class="col-span-2 lg:col-span-1">
                    {{-- <img src="/images/logo.png" alt="XileRetro Logo" class="w-1/2"> --}}
                    <div class="grid grid-cols-2">
                        <h2 class="text-xl font-bold my-6 text-gray-100">Navigate</h2>
                        <div class="grid grid-cols-2 col-span-2 col-gap-6 prose">
                            <ul class="m-0">
                                <li><a class="text-gray-300 no-underline hover:underline" href="{{ url('/') }}">Home</a></li>
                                <li><a target="_blank" class="text-gray-300 no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Server_Rules">Rulebook</a></li>
                                <li><a target="_blank" class="text-gray-300 no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Server_Information#Staff">The Staff</a></li>
                                <li><a class="text-gray-300 no-underline hover:underline" href="#steps2play">Download</a></li>
                                <li><a class="text-gray-300 no-underline hover:underline" href="#steps2play">Register</a></li>
                            </ul>
                            <ul class="m-0">
                                <li><a target="_blank" class="text-gray-300 no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Server_Information">Server Features</a></li>
                                <li><a target="_blank" class="text-gray-300 no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=General_Customs">Modified Official</a></li>
                                <li><a target="_blank" class="text-gray-300 no-underline hover:underline" href="https://wiki.xileretro.net/index.php?title=Category:Release_Notes">Updates & Release Notes</a></li>
                                <li><a target="_blank" class="text-gray-300 no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Newbie_Center">Newbie Center</a></li>
                                <li><a target="_blank" class="text-gray-300 no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Events">Events</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex items-center p-0 mt-8 status">
                        <p class="my-0 mt-12 ml-0 mr-6 text-5xl text-green-500 mb-5">Server is Online!</p>
                        <p class="px-2 text-sm text-gray-700 bg-gray-300 rounded">{{ cache()->remember('users', now()->addMinutes(1), function () { return App\Ragnarok\Char::query()->online()->count(); }) }}</p>
                    </div>
                    <p class="mb-20 text-gray-300">No errors to report</p>
                </div>
                <div class="col-span-2 lg:col-span-1">
                    <h2 class="text-xl font-bold my-6 text-gray-100">Community Loaders</h2>
                    <div class="grid grid-cols-3 screenshots">
                        <img class="col-span-1 my-0" src="images/loading/loading00.png" alt="Created by our players">
                        <img class="col-span-1 my-0" src="images/loading/loading06.png" alt="Created by our players">
                        <img class="col-span-1 my-0" src="images/loading/loading08.png" alt="Created by our players">
                    </div>
                    <div>
                        <h2 class="text-xl mt-20 font-bold my-6 text-gray-100">Get in Touch</h2>
                        <div class="grid grid-cols-3 col-gap-3">
                            <a href="https://www.facebook.com/groups/XileRetro" class="no-underline">
                                <div class="flex items-center col-span-1">
                                    <i class="mr-2 text-4xl text-blue-500 fab fa-facebook-square"></i>
                                    <p class="m-0 text-blue-100">Facebook Page</p>
                                </div>
                            </a>
                            <a href="https://discord.gg/hp7CS6k" class="no-underline">
                                <div class="flex items-center col-span-1">
                                    <i class="mr-2 text-4xl text-indigo-500 fab fa-discord"></i>
                                    <p class="m-0 text-indigo-100">Discord</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container px-3 md:px-0 grid grid-cols-3 pt-8 pb-10 mx-auto copyright">
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
    <script src="{{ asset('js/app.js') }}"></script>
    @livewireScripts
</body>
</html>
