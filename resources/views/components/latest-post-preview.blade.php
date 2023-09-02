<div>
    <section id="important-links" class="relative overflow-hidden py-16 md:pt-32 bg-black">
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            <h2>Latest Updates</h2>
            <p class="mt-6 text-gray-300 leading-relaxed">Welcome to the world of Xilero, where unique adventures await! If you're new to our server or looking to enhance your gameplay experience, you've come to the right place. Our Getting Started guides are crafted to help players of all levels navigate the distinct features and mechanics that set Xilero apart.</p>
            <div class="grid grid-cols-3 gap-12 mt-14">
                @foreach(\App\Models\Post::orderBy('created_at', 'desc')->take(3)->get() as $post)
                    <div class="col-span-1 md:col-span-2 lg:col-span-1 bg-gray-900 hover:bg-gray-800 rounded">
                        <a title="Essential Starter Packages & Guides for New Players" href="{{ route('posts.show', $post) }}">
                            <div class="p-6 rounded-md hover:shadow-lg prose">
{{--                                <div class="mb-6 border border-gray-200 rounded">--}}
{{--                                    <img class="object-cover w-full rounded h-44" style="margin:0" src="https://get.wallhere.com/photo/anime-Sword-Art-Online-Kirito-Sword-Art-Online-sword-1859509.jpg" alt="Starter Packages & Guids Image">--}}
{{--                                </div>--}}
                                <p class="mb-0 text-amber-500">{{ $post->created_at->diffForHumans() }}</p>
                                <h3 style="font-size: 1.5em" class="my-2 half-border font-normal text-gray-100">{{ $post->title }}</h3>
                                <p class="text-gray-100 mt-4">{{ $post->blurb }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
