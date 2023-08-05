@extends('layouts.app')

@section('title', 'XileRO Forums')

@section('content')

<section id="important-links" class="relative overflow-hidden py-16 md:pt-32 bg-black">
    <div class="max-w-screen-xl w-full mx-auto px-5 prose bg-gray-900 rounded p-6">
        <h2 class="text-4xl font-bold max-w-lg md:text-4xl text-gray-100">Forums</h2>
        <div class="text-gray-100">
            <p>It's wonderful to have you here! We're overjoyed you've discovered our site and are as excited about XileRO as we are. But the interaction extends beyond this platform.</p>
            <p>We encourage you to become part of our vibrant Discord community, a bustling center for instant conversations, exchanging ideas, and gaining knowledge. No matter if you're a seasoned pro or a beginner, our community has a spot just for you.</p>
            <p>To enhance your connection with us, click on "Join Discord". We're excitedly looking forward to having you onboard!</p>
        </div>
        <a href="https://discord.gg/hp7CS6k" class="btn-primary bg-indigo-500 rounded py-3 px-5 mt-6 no-underline text-white hover:text-white hover:bg-indigo-300 inline-block text-3xl">
            Join Discord
        </a>
    </div>
</section>

@endsection
