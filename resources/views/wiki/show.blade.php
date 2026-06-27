{{-- resources/views/wiki/show.blade.php --}}
<x-app-layout>
    @section('title', config("wiki.servers.$server.label") . ' Wiki: ' . $title)
    @section('description', $subtitle ?? 'XileRO Wiki')

    <div id="reading-progress" class="fixed top-0 left-0 w-0 h-1 bg-amber-500 z-50"></div>

    <div class="bg-gray-950 min-h-screen pt-20 lg:pt-24">
        <div class="max-w-screen-2xl mx-auto px-5 pb-16">

            {{-- Breadcrumbs --}}
            <nav class="flex flex-wrap items-center text-sm text-gray-400 py-4">
                <a href="/wiki" class="hover:text-amber-500">Wiki</a>
                @foreach ($breadcrumbs as $c)
                    <span class="mx-2 text-gray-600">/</span>
                    @if (! $loop->last)
                        <a href="{{ $c['url'] }}" class="hover:text-amber-500">{{ $c['name'] }}</a>
                    @else
                        <span class="text-amber-500 font-semibold">{{ $c['name'] }}</span>
                    @endif
                @endforeach
            </nav>

            <div class="flex flex-col lg:flex-row gap-8">
                {{-- LEFT: SUMMARY sidebar --}}
                <aside class="lg:w-64 lg:flex-shrink-0">
                    <div class="lg:sticky lg:top-24 max-h-[calc(100vh-7rem)] overflow-y-auto pr-2">
                        @foreach ($nav as $section)
                            @if ($section['title'])
                                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mt-5 mb-2">{{ $section['title'] }}</h3>
                            @endif
                            @include('wiki.partials.nav', ['items' => $section['items'], 'currentUrl' => $currentUrl])
                        @endforeach
                    </div>
                </aside>

                {{-- CENTER: content --}}
                <main class="flex-1 min-w-0">
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-100 mb-2">{{ $title }}</h1>
                    @if ($subtitle)
                        <p class="text-lg text-gray-400 mb-8">{{ $subtitle }}</p>
                    @endif
                    <article class="prose prose-invert prose-lg max-w-none wiki-content">
                        {!! $html !!}
                    </article>
                </main>
            </div>
        </div>
    </div>

    @include('wiki.partials.styles')
</x-app-layout>
