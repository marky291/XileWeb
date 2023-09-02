<x-app-layout>

    @section('title', 'XileRO | War of Emperium')
    @section('description', "Join the War of Emperium on XileRO. Engage in epic castle battles, explore real-time ownership, and find battle schedules across all timezones. Answer the call to glory today!")
    @section('keywords', 'Ragnarok Online, War of Emperium, Xilero, castle battles, private server, WoE times, MMO, online gaming, guild wars, PvP, real-time strategy')

    <section class="shadow bg-[url('../assets/landing-sitting.jpeg')] bg-cover md:pt-16">
        <div class="container grid px-3 sm:px-0 grid-cols-5 gap-4 mx-auto">
            <div class="col-span-5 pt-20 pb-4">
                <div class="p-8 rounded bg-zinc-900/90">
                    <div class="prose text-gray-300 tracking-normal text-lg">
                        <h1 class="mb-6 text-white text-3xl">{{ $post->title }}</h1>

                        <x-markdown class="markdown text-gray-100">
                            {{ $post->body }}
                        </x-markdown>
                    </div>
                </div>
            </div>


        </div>
    </section>


</x-app-layout>
