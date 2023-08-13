<div class="w-full absolute shadow bg-gray-900 bg-opacity-70">
    <header aria-label="Main Navigation" x-trap.inert.noscroll="navIsOpen" class="relative z-50 text-white" @keydown.window.escape="navIsOpen = false"
        @click.away="navIsOpen = false" aria-label="Main Navigation">
        <div class="relative max-w-screen-2xl mx-auto w-full py-4 transition duration-200 lg:bg-transparent lg:py-6">
            <div class="max-w-screen-xl mx-auto px-5 flex items-center justify-between">
                <div class="flex-1">
                    <a href="/" aria-label="Home Page" title="XileRO Home">
                        <h1 class="my-0 text-2xl py-0 font-bold">XileRO.net</h1>
                    </a>
                </div>
                <ul aria-label="Desktop Navigation Menu" class="relative hidden lg:flex lg:items-center lg:justify-center lg:gap-6 xl:gap-10">
                    <li><a class="rounded py-3 px-6 text-gray-100 hover:bg-amber-500 hover:text-gray-900 border-gray-500 hover:bg-opacity-90 bg-gray-900 bg-opacity-50"
                            href="https://xileretro.net/#steps2play" title="Register at XileRO">Register</a></li>
                    <li><a class="rounded py-3 px-6 text-gray-100 hover:bg-amber-500 hover:text-gray-900 border-gray-500 hover:bg-opacity-90 bg-gray-900 bg-opacity-50"
                            href="http://wiki.xileretro.net/index.php?title=Donation"
                            title="Donate to XileRO">Donate</a></li>
                    <li><a class="rounded py-3 px-6 text-gray-100 hover:bg-amber-500 hover:text-gray-900 border-gray-500 hover:bg-opacity-90 bg-gray-900 bg-opacity-50"
                            href="https://discord.gg/hp7CS6k" title="Join XileRO on Discord">Discord</a></li>
                    <li><a class="rounded py-3 px-6 text-gray-100 hover:bg-amber-500 hover:text-gray-900 border-gray-500 hover:bg-opacity-90 bg-gray-900 bg-opacity-50"
                            href="http://wiki.xileretro.net/index.php?title=Main_Page" title="XileRO Wiki Page">Wiki</a>
                    </li>
                </ul>
                <div class="flex-1 flex items-center justify-end">
                    <a target="_blank" href="https://wiki.xileretro.net/index.php?search=&title=Special%3ASearch&go=Go"
                        title="Search XileRO Wiki">
                        <button id="search_button" aria-label="Search XileRO Wiki">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </a>

                    <a target="_blank"
                        class="group relative inline-flex border border-amber-600 focus:outline-none hidden lg:ml-4 lg:inline-flex"
                        href="https://drive.google.com/file/d/1Kf6R_IF6VqSy_ZcqspbqgK0LAFAfA11o/view?usp=sharing"
                        title="Download XileRO">
                        <span
                            class="w-full inline-flex items-center justify-center self-stretch px-4 py-2 text-sm text-gray-900 text-center font-bold uppercase bg-amber-500 ring-1 ring-amber-600 ring-offset-1 transform transition-transform group-hover:-translate-y-1  group-focus:-translate-y-1 group-focus:-translate-x-1">
                            Download
                        </span>
                    </a>
                    <button aria-label="Toggle Mobile Menu"
                        class="ml-2 relative w-10 h-10 inline-flex items-center justify-center p-2 text-white lg:hidden"
                        aria-label="Toggle Menu" @click.prevent="navIsOpen = !navIsOpen">
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
                        <li>
                            <a class="block w-full py-4 text-rose-800"
                                href="http://wiki.xileretro.net/index.php?title=Donation" title="Donate to XileRO"
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
                                href="http://wiki.xileretro.net/index.php?title=Main_Page"
                                title="Access XileRO's Wiki Page" aria-label="Access XileRO's Wiki Page">
                                Wiki
                            </a>
                        </li>
                        <li>
                            <a class="block w-full py-4 text-rose-800" href="https://xileretro.net/#steps2play"
                                title="Register for XileRO" aria-label="Register for XileRO">
                                Register
                            </a>
                        </li>
                        <li class="flex sm:justify-center">
                            <a class="group relative inline-flex border border-red-600 focus:outline-none mt-3 w-full max-w-md"
                                href="https://drive.google.com/file/d/1Kf6R_IF6VqSy_ZcqspbqgK0LAFAfA11o/view?usp=sharing"
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
    </header>
</div>
