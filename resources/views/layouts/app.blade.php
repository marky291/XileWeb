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
        <meta name="description" content="@yield('description', 'XileRO is a leading PK server, offering unique mechanics and one-of-a-kind classic style gameplay. Experience XileRO today and join the immersive world that redefines PK gaming.')">
        <meta name="keywords" content="@yield('keywords', 'Ragnarok, Ragnarok Online, Classic, RO, XileRO, XileRO PK, PK Server, Woe Server')">
        <meta name="author" content="XileRO">
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
        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://kit.fontawesome.com/0e7a67f5fc.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
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

    <!-- Particles.js Background -->
    <div id="particles-js" class="fixed inset-0 z-50 pointer-events-none"></div>

    <script src="https://cdn.jsdelivr.net/npm/@widgetbot/crate@3" async defer>
        const crate = new Crate({
            server: '702319926110584943',
            channel: '702319926500655135'
        })
        crate.notify("⭒❃.✮: Welcome to XileRO, Dont forget to join our discord community! :✮.❃⭒")
    </script>

        <div class="min-h-screen">


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

                <footer id="footer" class="bg-clash-foot border-t border-gray-800">
                    {{-- Main Footer Content --}}
                    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-16">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8">

                            {{-- Brand & Server Status --}}
                            <div class="lg:col-span-3">
                                <a href="/" class="inline-block mb-6">
                                    <span class="text-3xl font-bold tracking-tight">
                                        <span class="text-white">Xile</span><span class="text-xilero-gold">RO</span>
                                    </span>
                                </a>
                                <p class="text-gray-400 text-sm mb-6">The ultimate classic Ragnarok Online experience with unique mechanics and one-of-a-kind gameplay.</p>

                                {{-- Server Status --}}
                                <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700/50 space-y-3">
                                    <div class="flex items-center gap-3">
                                        <span class="relative flex h-3 w-3">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                        </span>
                                        <span class="text-gray-300 text-sm">XileRO</span>
                                        <span class="text-green-400 text-xs font-medium">Online</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="relative flex h-3 w-3">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                        </span>
                                        <span class="text-gray-300 text-sm">XileRetro</span>
                                        <span class="text-green-400 text-xs font-medium">Online</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Quick Links --}}
                            <div class="lg:col-span-2">
                                <h3 class="text-xilero-gold font-semibold text-sm uppercase tracking-wider mb-4">Navigate</h3>
                                <ul class="space-y-3">
                                    <li><a href="{{ url('/') }}" class="text-gray-400 hover:text-white transition-colors text-sm">Home</a></li>
                                    <li><a href="{{ route('donate-shop') }}" class="text-gray-400 hover:text-white transition-colors text-sm">Donate Shop</a></li>
                                    <li><a href="{{ route('register') }}" class="text-gray-400 hover:text-white transition-colors text-sm">Register</a></li>
                                    <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition-colors text-sm">Login</a></li>
                                    <li><a href="{{ route('password.request') }}" class="text-gray-400 hover:text-white transition-colors text-sm">Forgot Password</a></li>
                                </ul>
                            </div>

                            {{-- Resources --}}
                            <div class="lg:col-span-2">
                                <h3 class="text-xilero-gold font-semibold text-sm uppercase tracking-wider mb-4">Resources</h3>
                                <ul class="space-y-3">
                                    <li><a href="https://info.xilero.net" target="_blank" rel="noopener" class="text-gray-400 hover:text-white transition-colors text-sm">XileRO Wiki</a></li>
                                    <li><a href="https://wiki.xilero.net" target="_blank" rel="noopener" class="text-gray-400 hover:text-white transition-colors text-sm">XileRetro Wiki</a></li>
                                    <li><a href="https://discord.gg/hp7CS6k" target="_blank" rel="noopener" class="text-gray-400 hover:text-white transition-colors text-sm">Discord Community</a></li>
                                    <li><a href="https://www.facebook.com/groups/XileRetro" target="_blank" rel="noopener" class="text-gray-400 hover:text-white transition-colors text-sm">Facebook Group</a></li>
                                    <li><a href="https://discord.gg/pvXGhChQyh" target="_blank" rel="noopener" class="text-gray-400 hover:text-white transition-colors text-sm">Support Ticket</a></li>
                                </ul>
                            </div>

                            {{-- Community Art --}}
                            <div class="lg:col-span-5">
                                <h3 class="text-xilero-gold font-semibold text-sm uppercase tracking-wider mb-4">Community Fan Art</h3>
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="aspect-[4/3] rounded-lg overflow-hidden border border-gray-700/50 hover:border-xilero-gold/50 transition-colors">
                                        <img class="w-full h-full object-cover" src="{{ url('images/loading/loading00.png') }}" alt="Community Art 1">
                                    </div>
                                    <div class="aspect-[4/3] rounded-lg overflow-hidden border border-gray-700/50 hover:border-xilero-gold/50 transition-colors">
                                        <img class="w-full h-full object-cover" src="{{ url('images/loading/loading06.png') }}" alt="Community Art 2">
                                    </div>
                                    <div class="aspect-[4/3] rounded-lg overflow-hidden border border-gray-700/50 hover:border-xilero-gold/50 transition-colors">
                                        <img class="w-full h-full object-cover" src="{{ url('images/loading/loading08.png') }}" alt="Community Art 3">
                                    </div>
                                </div>
                                <p class="text-gray-500 text-xs mt-3">Created by talented players in our community</p>

                                {{-- Social Links --}}
                                <div class="flex items-center gap-4 mt-6">
                                    <a href="https://discord.gg/hp7CS6k" target="_blank" rel="noopener" class="text-gray-400 hover:text-indigo-400 transition-colors" title="Join Discord">
                                        <i class="fab fa-discord text-2xl"></i>
                                    </a>
                                    <a href="https://www.facebook.com/groups/XileRetro" target="_blank" rel="noopener" class="text-gray-400 hover:text-blue-400 transition-colors" title="Facebook Group">
                                        <i class="fab fa-facebook text-2xl"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom Bar --}}
                    <div class="border-t border-gray-800">
                        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-6">
                            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-3 text-gray-400">
                                    <i class="far fa-heart text-xilero-gold"></i>
                                    <p class="text-sm">Thank you to all players who support our community</p>
                                </div>
                                <div class="text-gray-500 text-sm">
                                    <span class="text-gray-400">XileRO</span> · Version {{ config('app.version') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <!-- Particles.js Initialization -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof particlesJS !== 'undefined') {
                    particlesJS('particles-js', {
                        "particles": {
                            "number": {
                                "value": 160,
                                "density": {
                                    "enable": true,
                                    "value_area": 800
                                }
                            },
                            "color": {
                                "value": "#ffffff"
                            },
                            "shape": {
                                "type": "circle",
                                "stroke": {
                                    "width": 0,
                                    "color": "#000000"
                                }
                            },
                            "opacity": {
                                "value": 0.12827296486924183,
                                "random": true,
                                "anim": {
                                    "enable": true,
                                    "speed": 1,
                                    "opacity_min": 0,
                                    "sync": false
                                }
                            },
                            "size": {
                                "value": 3,
                                "random": true,
                                "anim": {
                                    "enable": false,
                                    "speed": 4,
                                    "size_min": 0.3,
                                    "sync": false
                                }
                            },
                            "line_linked": {
                                "enable": false,
                                "distance": 150,
                                "color": "#ffffff",
                                "opacity": 0.4,
                                "width": 1
                            },
                            "move": {
                                "enable": true,
                                "speed": 1,
                                "direction": "none",
                                "random": true,
                                "straight": false,
                                "out_mode": "out",
                                "bounce": false,
                                "attract": {
                                    "enable": false,
                                    "rotateX": 600,
                                    "rotateY": 600
                                }
                            }
                        },
                        "interactivity": {
                            "detect_on": "canvas",
                            "events": {
                                "onhover": {
                                    "enable": false,
                                    "mode": "repulse"
                                },
                                "onclick": {
                                    "enable": true,
                                    "mode": "repulse"
                                },
                                "resize": true
                            },
                            "modes": {
                                "grab": {
                                    "distance": 400,
                                    "line_linked": {
                                        "opacity": 1
                                    }
                                },
                                "bubble": {
                                    "distance": 250,
                                    "size": 0,
                                    "duration": 2,
                                    "opacity": 0,
                                    "speed": 3
                                },
                                "repulse": {
                                    "distance": 400,
                                    "duration": 0.4
                                },
                                "push": {
                                    "particles_nb": 4
                                },
                                "remove": {
                                    "particles_nb": 2
                                }
                            }
                        },
                        "retina_detect": true
                    });
                }
            });
        </script>
        @livewireScripts
        @filamentScripts
    </body>
</html>
