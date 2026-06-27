{{-- resources/views/wiki/home.blade.php --}}
<x-app-layout>
    @section('title', 'XileRO Wiki')

    <section class="bg-gray-950 min-h-screen pt-24 pb-16">
        <div class="max-w-screen-lg mx-auto px-5 text-center">
            <h1 class="text-5xl font-bold text-gray-100 mb-4">XileRO <span class="text-amber-500">Wiki</span></h1>
            <p class="text-xl text-gray-400 mb-12">Choose your server</p>

            <div class="grid md:grid-cols-2 gap-6">
                @foreach ($servers as $s)
                    <a href="{{ $s['url'] }}"
                       class="block bg-gray-900 border border-gray-800 hover:border-amber-500 rounded-lg p-8 transition-colors {{ $s['available'] ? '' : 'opacity-60' }}">
                        <div class="text-3xl font-bold text-amber-500 mb-2">{{ $s['label'] }}</div>
                        <div class="text-gray-400">{{ $s['rate'] }}</div>
                        @unless ($s['available'])
                            <div class="mt-3 text-sm text-gray-500">coming soon</div>
                        @endunless
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</x-app-layout>
