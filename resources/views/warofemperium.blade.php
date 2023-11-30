<x-app-layout>

    @section('title', 'XileRO | War of Emperium (WOE)')
    @section('description', "Join the War of Emperium (WOE) on XileRO. Engage in epic castle battles, explore real-time ownership, and find battle schedules across all timezones. Answer the call to glory today!")
    @section('keywords', 'Ragnarok Online, War of Emperium, Xilero, castle battles, private server, WoE times, MMO, online gaming, guild wars, PvP, real-time strategy')

    <section class="shadow bg-[url('../assets/landing-sitting.jpeg')] bg-cover md:pt-16">
        <div class="container grid px-3 sm:px-0 grid-cols-5 gap-4 mx-auto">
            <div class="col-span-5 pt-20 pb-4">
                <div class="p-8 rounded bg-zinc-900/90">
                    <div class="prose text-gray-300 tracking-normal text-lg">
                        <h1 class="mb-6 text-white text-3xl">War of Emperium</h1>
                        <p>Embark on a thrilling adventure in the War of Emperium on Xilero, where the bravest warriors and the most cunning strategists come to conquer castles and claim victory. Engage in epic battles, forge powerful alliances, and take part in the grandest war in our exclusive Ragnarok Online server. Explore real-time castle ownership updates, and seize your opportunity to become a legend. The War of Emperium awaits you – are you ready to answer the call?</p>
                    </div>

                    <section id="prontera-castles" class="relative overflow-hidden py-16">
                        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
                            <div class="container mx-auto rounded">
                                <div class="">
                                    <h2>Woe Times</h2>
                                    <p class="mt-6 text-gray-300 leading-relaxed mb-8">Prepare for battle and mark your calendars! The War of Emperium on Xilero takes place across various timezones, ensuring that warriors from all corners of the world can join the fight. Find the schedule that fits your timezone below and rally your guild for the epic clashes in the specified castles:</p>
                                </div>
                                <div class="grid grid-cols-{{$castles->count()}} gap-5">
                                    @foreach ($castles as $castle)
                                        <div class="mb-4 bg-gray-900 bg-opacity-70 rounded p-5">
                                            <h3 class="text-amber-500 text-2xl mb-8 half-border">{{ $castle->name }}</h3>
                                            @foreach(config('castles.timezones') as $timezone)
                                                <div class="text-white mb-8 flex rounded">
                                                    <div class="mr-3">
                                                        @if(!is_null($castle->guild)->hasEmblem())
                                                            <div class="w-8 h-8 m-0 shadow" style="background: url('{{ url($castle->guild->emblem) }}'); background-size:contain;"></div>
                                                        @else
                                                            <img class="h-8 w-8 m-0" src="/assets/emblems/empty.bmp"/>
                                                        @endif
                                                    </div>
                                                    <div class="">
                                                        <h4 class="text-white font-bold mb-1">{{ $timezone }}</h4>
                                                        @foreach(config("castles.prontera.{$castle->name}.day") as $day)
                                                                <?php
                                                                $date = new DateTime();
                                                                $date->modify("next {$day}");
                                                                $time = DateTime::createFromFormat("H:i", config("castles.prontera.{$castle->name}.time"));
                                                                $date->setTime($time->format('H'), $time->format('i'))->modify(config('castles.modifier'));
                                                                ?>
                                                            <p class="mt-1 text-gray-400">{{ $date->setTimezone(new DateTimeZone($timezone))->format("l, H:i A") }}</p>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>


        </div>
    </section>


</x-app-layout>
