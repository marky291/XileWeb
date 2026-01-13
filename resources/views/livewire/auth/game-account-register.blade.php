@section('title', 'Register - XileRO')

<section class="bg-clash-bg min-h-screen flex items-center justify-center pt-28 pb-16 px-4">
    <div class="w-full max-w-md">
        <div class="block-home bg-gray-900 p-8 rounded-lg">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Register</h1>
                <p class="text-gray-400">
                    Register your game account
                </p>
            </div>

            <form wire:submit="register" class="space-y-6">
                {{-- Username --}}
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-300 mb-2">
                        Username
                    </label>
                    <input
                        wire:model.live="username"
                        type="text"
                        id="username"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        placeholder="username"
                        required
                        autofocus
                    >
                    <p class="mt-2 text-xs text-gray-500">4-23 characters, letters and numbers only</p>
                    @error('username')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                        Email address
                    </label>
                    <input
                        wire:model.live="email"
                        type="email"
                        id="email"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        placeholder="you@example.com"
                        required
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                        Password
                    </label>
                    <input
                        wire:model="password"
                        type="password"
                        id="password"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        placeholder="••••••••"
                        required
                    >
                    <p class="mt-2 text-xs text-gray-500">6-31 characters</p>
                    @error('password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                        Confirm password
                    </label>
                    <input
                        wire:model="password_confirmation"
                        type="password"
                        id="password_confirmation"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        placeholder="••••••••"
                        required
                    >
                </div>

                {{-- Submit Button --}}
                <button
                    type="submit"
                    class="w-full px-4 py-3 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-lg transition-colors duration-200 flex items-center justify-center"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-75 cursor-wait"
                >
                    <span wire:loading.remove>Sign up</span>
                    <span wire:loading class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating account...
                    </span>
                </button>
            </form>

            <p class="mt-6 text-center text-gray-500 text-sm">
                Free to play. No donations required.
            </p>
        </div>
    </div>
</section>
