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

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Styles -->
    <link href="{{ mix('assets/core.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" rel="stylesheet">
</head>
<body class="h-screen antialiased leading-none">

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

    <div id="app" class="prose max-w-none">

        <div style="background-color: #6e3755" class="shadow">
            <div class="container mx-auto px-3 md:px-0">
                <nav class="flex flex-wrap items-center justify-between py-6 pt-7">
                    <div class="flex-grow block w-full lg:flex lg:items-center lg:w-auto">
                        <div class="lg:flex-grow">
                        <a href="{{ url('/') }}" class="block mt-4 mr-6 font-bold text-white no-underline border-b-2 lg:inline-block lg:mt-0 border-red-50">
                            Home
                        </a>
                        <a target="_blank" href="http://wiki.xileretro.net/index.php?title=Donation" class="block mt-4 mr-6 text-white no-underline border-b-2 border-transparent lg:inline-block lg:mt-0">
                            Donate
                        </a>
                        <a target="_blank" href="http://wiki.xileretro.net/index.php?title=Main_Page" class="block mt-4 mr-6 text-white no-underline border-b-2 border-transparent lg:inline-block lg:mt-0">
                            Guides
                        </a>
                        <a target="_blank" href="http://wiki.xileretro.net/index.php?title=Main_Page" class="block mt-4 mr-6 text-white no-underline border-b-2 border-transparent lg:inline-block lg:mt-0">
                            Packages
                        </a>
                        <a href="#steps2play" class="block mt-4 mr-6 text-white no-underline border-b-2 border-transparent lg:inline-block lg:mt-0">
                            Register
                        </a>
                        </div>
                        <div class="hidden lg:block">
                            <a target="_blank" href="{{ config('downloads.full')[array_key_last(config('downloads.full'))]['link'] }}" style="color: #6e3755;" class="inline-block px-4 py-2 mt-4 text-sm leading-none text-white no-underline rounded hover:border hover:border-white lg:mt-0">
                                <span class="mr-2">Download</span> <i class="fas fa-download"></i>
                            </a>
                        </div>

                    </div>
                </nav>
            </div>
        </div>

        @yield('content')

        <footer id="footer" class="shadow-inner">
            <div class="container px-3 md:px-0 grid grid-cols-2 col-gap-1 pt-5 mx-auto pb-15 content">
                <div class="col-span-2 lg:col-span-1">
                    {{-- <img src="/images/logo.png" alt="XileRetro Logo" class="w-1/2"> --}}
                    <div class="grid grid-cols-2">
                        <h2>Navigate</h2>
                        <div class="grid grid-cols-2 col-span-2 col-gap-6">
                            <ul>
                                <li><a class="no-underline hover:underline" href="{{ url('/') }}">Home</a></li>
                                <li><a target="_blank" class="no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Server_Rules">Rulebook</a></li>
                                <li><a target="_blank" class="no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Server_Information#Staff">The Staff</a></li>
                                <li><a class="no-underline hover:underline" href="#steps2play">Download</a></li>
                                <li><a class="no-underline hover:underline" href="#steps2play">Register</a></li>
                            </ul>
                            <ul>
                                <li><a target="_blank" class="no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Server_Information">Server Features</a></li>
                                <li><a target="_blank" class="no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=General_Customs">Modified Official</a></li>
                                <li><a target="_blank" class="no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Updates_Changelog">Updates & Changelog</a></li>
                                <li><a target="_blank" class="no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Newbie_Center">Newbie Center</a></li>
                                <li><a target="_blank" class="no-underline hover:underline" href="http://wiki.xileretro.net/index.php?title=Events">Events</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex items-center p-0 mt-8 status">
                        <p class="my-0 ml-0 mr-6 text-5xl text-green-500">Server is Online!</p>
                        <p class="px-2 text-sm text-gray-700 bg-gray-300 rounded">{{ cache()->remember('users', now()->addMinutes(1), function () { return App\Ragnarok\Char::query()->online()->count(); }) }}</p>
                    </div>
                    <p class="m-0">No errors to report</p>

                </div>
                <div class="col-span-2 lg:col-span-1">
                    <h2>Community Loaders</h2>
                    <div class="grid grid-cols-3 screenshots">
                        <img class="col-span-1 my-0" src="images/loading/loading00.png" alt="Created by our players">
                        <img class="col-span-1 my-0" src="images/loading/loading06.png" alt="Created by our players">
                        <img class="col-span-1 my-0" src="images/loading/loading08.png" alt="Created by our players">
                    </div>
                    <div>
                        <h2>Get in Touch</h2>
                        <div class="grid grid-cols-3 col-gap-3">
                            <a href="https://www.facebook.com/groups/XileRetro" class="no-underline hover:underline">
                                <div class="flex items-center col-span-1">
                                    <i class="mr-2 text-4xl text-blue-600 fab fa-facebook-square"></i>
                                    <p class="m-0">Facebook Page</p>
                                </div>
                            </a>
                            <a href="https://www.facebook.com/groups/1671355556362607" class="no-underline hover:underline">
                                <div class="flex items-center col-span-1">
                                    <i class="mr-2 text-4xl text-blue-500 fab fa-facebook-messenger"></i>
                                    <p class="m-0">Facebook Group</p>
                                </div>
                            </a>
                            <a href="discord.gg/hp7CS6k" class="no-underline hover:underline">
                                <div class="flex items-center col-span-1">
                                    <i class="mr-2 text-4xl text-indigo-600 fab fa-discord"></i>
                                    <p class="m-0">Discord</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container px-3 md:px-0 grid grid-cols-3 pt-8 pb-10 mx-auto copyright">
                <div class="flex items-center col-span-3 lg:col-span-2 lg:text-left text-gray-700">
                    <i class="mr-4 text-5xl far fa-heart"></i>
                    <p>A thank you to all our players who support and show commitment<br>to making a server that's great for everyone.</p>
                </div>
                <div class="hidden lg:flex items-end justify-end col-span-3 lg:col-span-1 text-right">
                    <p>Website design and coded by <a href="https://www.facebook.com/Marky291">Mark Hester</a><br><span class="text-gray-400">Version {{ config('app.version') }}</span></p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
