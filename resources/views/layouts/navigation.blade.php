<nav class="w-full absolute shadow bg-black/70">
    <div aria-label="Main Navigation" x-trap.inert.noscroll="navIsOpen" class="relative z-50 text-white" @keydown.window.escape="navIsOpen = false"
         @click.away="navIsOpen = false">
        <div class="relative max-w-screen-2xl mx-auto w-full py-4 transition duration-200 lg:bg-transparent lg:py-6">
            <div class="max-w-screen-xl mx-auto px-5 flex items-center justify-between">
                <div class="flex-1">
                    <a href="/" aria-label="Home Page" title="XileRO Home">
                        <h2 class="my-0 text-2xl py-0 font-bold">XiLeRO.NET</h2>
                    </a>
                </div>
                <ul aria-label="Desktop Navigation Menu" class="relative hidden lg:flex lg:items-center lg:justify-center lg:gap-6 xl:gap-10">
                    @guest
                        <li><a class="hover:bg-linear-to-bl hover:from-blue-900 hover:to-fuchsia-700 rounded-xs py-3 px-6 text-gray-100 border-gray-500 bg-gray-900/50"
                               href="{{ route('register') }}" title="Register at XileRO">Register</a></li>
                        <li><a class="hover:bg-linear-to-bl hover:from-green-700 hover:to-emerald-500 rounded-xs py-3 px-6 text-gray-100 border-gray-500 bg-gray-900/50"
                               href="{{ route('login') }}" title="Login to XileRO">Login</a></li>
                    @else
                        <li><a class="hover:bg-linear-to-bl hover:from-blue-900 hover:to-fuchsia-700 rounded-xs py-3 px-6 text-gray-100 border-gray-500 bg-gray-900/50"
                               href="{{ route('dashboard') }}" title="My Account">My Account</a></li>
                    @endguest
                    <li><a class="hover:bg-linear-to-tr hover:from-blue-900 hover:to-fuchsia-700 rounded-xs py-3 px-6 text-gray-100 border-gray-500 bg-gray-900/50"
                           href="https://discord.com/channels/702319926110584943/1150037346415284284"
                           title="Donate to XileRO">Donate</a></li>
                    <li><a class="hover:bg-linear-to-bl hover:from-blue-900 hover:to-fuchsia-700 rounded-xs py-3 px-6 text-gray-100 border-gray-500 bg-gray-900/50"
                           href="https://discord.gg/hp7CS6k" title="Join XileRO on Discord">Discord</a></li>
                    <li><a class="hover:bg-linear-to-tr hover:from-blue-900 hover:to-fuchsia-700 rounded-xs py-3 px-6 text-gray-100 border-gray-500 bg-gray-900/50"
                            href="http://wiki.xilero.net/index.php?title=Main_Page" title="XileRO Wiki Page">Wiki</a>
                     </li>
                </ul>
                <div class="flex-1 flex items-center justify-end">
                    <a target="_blank" rel="noopener" href="https://wiki.xilero.net/index.php?search=&title=Special%3ASearch&go=Go"
                       title="Search XileRO Wiki">
                        <button id="search_button" aria-label="Search XileRO Wiki">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </a>
                    <a target="_blank" rel="noopener"
                       class="btn btn-primary ml-12"
                       href="https://drive.google.com/file/d/1RDUNM6pWVBMVLzdMVyze5QmdNuc99zKH/view?usp=sharing"
                       title="Download XileRO">
                        <span
                            class="">
                            Download
                        </span>
                    </a>
                    <button aria-label="Toggle Mobile Menu"
                            class="ml-2 relative w-10 h-10 inline-flex items-center justify-center p-2 text-white lg:hidden"
                            @click.prevent="navIsOpen = !navIsOpen">
                        <svg x-show="! navIsOpen" class="w-6" viewBox="0 0 28 12" fill="none"
                             xmlns="http://www.w3.org/2000/svg" style="">
                            <line y1="1" x2="28" y2="1" stroke="currentColor" stroke-width="2">
                            </line>
                            <line y1="11" x2="28" y2="11" stroke="currentColor" stroke-width="2">
                            </line>
                        </svg>
                        <svg x-show="navIsOpen" class="absolute inset-0 mt-2.5 ml-2.5 w-5" viewBox="0 0 19 19"
                             fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                            <rect y="1.41406" width="2" height="24" transform="rotate(-45 0 1.41406)"
                                  fill="currentColor"></rect>
                            <rect width="2" height="24"
                                  transform="matrix(0.707107 0.707107 0.707107 -0.707107 0.192383 16.9707)"
                                  fill="currentColor"></rect>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div x-show="navIsOpen" aria-label="Mobile Navigation Menu" class="lg:hidden" x-transition:enter="duration-150"
             x-transition:leave="duration-100 ease-in" style="display: none;">
            <nav x-show="navIsOpen" x-transition.opacity=""
                 class="fixed inset-0 w-full pt-[4.2rem] z-10 pointer-events-none" style="display: none;">
                <div class="relative h-full w-full py-8 px-5 bg-white pointer-events-auto overflow-y-auto">
                    <ul>
                        @guest
                            <li>
                                <a class="block w-full py-4 text-rose-800" href="{{ route('register') }}"
                                   title="Register for XileRO" aria-label="Register for XileRO">
                                    Register
                                </a>
                            </li>
                            <li>
                                <a class="block w-full py-4 text-green-700 font-bold" href="{{ route('login') }}"
                                   title="Login to XileRO" aria-label="Login to XileRO">
                                    Login
                                </a>
                            </li>
                        @else
                            <li>
                                <a class="block w-full py-4 text-rose-800" href="{{ route('dashboard') }}"
                                   title="My Account" aria-label="My Account">
                                    My Account
                                </a>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full py-4 text-left text-gray-600"
                                            title="Logout" aria-label="Logout">
                                        Logout
                                    </button>
                                </form>
                            </li>
                        @endguest
                        <li>
                            <a class="block w-full py-4 text-rose-800"
                               href="https://discord.com/channels/702319926110584943/1150037346415284284" title="Donate to XileRO"
                               aria-label="Donate to XileRO">
                                Donate
                            </a>
                        </li>
                        <li>
                            <a class="block w-full py-4 text-rose-800" href="https://discord.gg/hp7CS6k"
                               title="Join the XileRO Discord Community"
                               aria-label="Join the XileRO Discord Community">
                                Community
                            </a>
                        </li>
                        <li>
                            <a class="block w-full py-4 text-rose-800"
                               href="http://wiki.xilero.net/index.php?title=Main_Page"
                               title="Access XileRO's Wiki Page" aria-label="Access XileRO's Wiki Page">
                                Wiki
                            </a>
                        </li>
                        <li class="flex sm:justify-center">
                            <a class="group relative inline-flex border border-red-600 focus:outline-hidden mt-3 w-full max-w-md"
                               href="https://drive.google.com/file/d/1RDUNM6pWVBMVLzdMVyze5QmdNuc99zKH/view?usp=sharing"
                               title="Download XileRO Game Client" aria-label="Download XileRO Game Client">
                                <span
                                    class="w-full inline-flex items-center justify-center self-stretch px-4 py-2 text-sm text-red-600 text-center font-bold uppercase bg-white ring-1 ring-red-600 ring-offset-1 transform transition-transform group-hover:-translate-y-1 group-hover:-translate-x-1 group-focus:-translate-y-1 group-focus:-translate-x-1">
                                    Download
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</nav>
