<x-app-layout>

    @section("title", "XileRO | Update: " . $post->title)
    @section('description', $post->patcher_notice)
    @section('keywords', 'XileRO, Updates, Changelog')

    <section class="shadow md:pt-16 bg-gray-800">
        <div class="container grid px-3 sm:px-0 grid-cols-5 gap-4 mx-auto">
            <div class="col-span-5 pt-20 pb-4 bg-gray-900">
                <div class="p-8 rounded">
                    <div class="prose text-gray-300 tracking-normal text-lg">
                        <p class="text-amber-500 mb-4">{{ $post->created_at->diffForHumans() }}</p>
                        <h1 class="mb-6 text-white text-3xl">{{ $post->title }}</h1>

                        <x-markdown class="markdown text-gray-100">
                            {{ $post->article_content }}
                        </x-markdown>
                    </div>
                </div>
            </div>


        </div>
    </section>


</x-app-layout>
