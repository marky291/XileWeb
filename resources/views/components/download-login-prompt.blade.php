<div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16">
    {{-- Left Column: Sign In --}}
    <div>
        <h2 class="mt-0 mb-2 text-2xl font-bold text-gray-100">
            <span class="mr-2">1.</span> Sign In
        </h2>
        <p class="mb-8 text-amber-500">Create a free account or log in to download.</p>

        <div class="grid gap-4">
            <a href="{{ route('register') }}" class="no-underline truncate text-gray-900 btn text-left btn-primary">
                <i class="fas fa-user-plus mr-3"></i>
                Register Now
            </a>
            <a href="{{ route('login') }}" class="no-underline truncate text-gray-900 btn text-left btn-secondary">
                <i class="fas fa-sign-in-alt mr-3"></i>
                Log In
            </a>
        </div>

        <p class="mt-6 text-gray-500 text-sm">
            <i class="fas fa-clock mr-2"></i>
            Registration takes less than 30 seconds
        </p>
    </div>

    {{-- Right Column: Download Preview (locked) --}}
    <div class="opacity-40 relative">
        <div class="absolute inset-0 flex items-center justify-center z-10">
            <div class="bg-gray-900/90 border border-gray-700 rounded-lg px-6 py-3 flex items-center gap-3">
                <i class="fas fa-lock text-amber-500"></i>
                <span class="text-gray-300 font-medium">Sign in to unlock</span>
            </div>
        </div>

        <h2 class="mt-0 mb-2 text-2xl font-bold text-gray-500">
            <span class="mr-2">2.</span> Download
        </h2>
        <p class="mb-8 text-gray-600">Windows & Android clients available.</p>

        <div class="grid gap-4 pointer-events-none select-none">
            <div class="btn bg-gray-800/40 text-gray-600 border border-gray-700/30 text-left truncate">
                <i class="fa fa-windows mr-3"></i>
                Full Client (3GB)
            </div>
            <div class="btn bg-gray-800/40 text-gray-600 border border-gray-700/30 text-left truncate">
                <i class="fa fa-android mr-3"></i>
                Android APK (3MB)
            </div>
        </div>
    </div>
</div>
