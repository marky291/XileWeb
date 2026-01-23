<div class="block-home bg-gray-900/80 border border-gray-700/50 rounded-lg p-6">
    <div class="flex items-center gap-4">
        <div class="shrink-0 w-12 h-12 rounded-full bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
            <i class="fas fa-lock text-amber-500 text-lg"></i>
        </div>
        <div class="flex-1">
            <h4 class="text-gray-100 font-semibold mb-1">Account Required</h4>
            <p class="text-gray-400 text-sm mb-0">Create a free account to access all downloads.</p>
        </div>
    </div>
    <div class="flex gap-3 mt-5">
        <a href="{{ route('login') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-lg transition-colors text-sm">
            <i class="fas fa-sign-in-alt"></i>
            Log In
        </a>
        <a href="{{ route('register') }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-700 hover:bg-gray-600 text-gray-100 font-semibold rounded-lg transition-colors border border-gray-600 text-sm">
            <i class="fas fa-user-plus"></i>
            Register
        </a>
    </div>
</div>
