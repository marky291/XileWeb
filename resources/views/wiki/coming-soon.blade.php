{{-- resources/views/wiki/coming-soon.blade.php --}}
<x-app-layout>
    @section('title', $label . ' Wiki — Coming Soon')

    <section class="bg-gray-950 min-h-screen pt-24 pb-16">
        <div class="max-w-screen-md mx-auto px-5 text-center">
            <h1 class="text-4xl font-bold text-gray-100 mb-4">{{ $label }} Wiki</h1>
            <p class="text-xl text-gray-400">This wiki is <span class="text-amber-500">coming soon</span>.</p>
            <a href="/wiki" class="inline-block mt-8 text-amber-500 hover:underline">← Back to wiki home</a>
        </div>
    </section>
</x-app-layout>
