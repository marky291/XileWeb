@section('title', 'Login - XileRO')

<section class="bg-clash-bg min-h-screen flex items-center justify-center pt-28 pb-16 px-4">
    <div class="w-full max-w-md">
        <div class="bg-gray-900 p-8 rounded-lg">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">Sign in</h1>
                <p class="text-gray-400">
                    or
                    <a href="{{ route('register') }}" class="text-amber-500 hover:text-amber-400">
                        sign up for an account
                    </a>
                </p>
            </div>

            <form wire:submit="authenticate" class="space-y-6">
                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                        Email address
                    </label>
                    <input
                        wire:model="email"
                        type="email"
                        id="email"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        placeholder="you@example.com"
                        required
                        autofocus
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-medium text-gray-300">
                            Password
                        </label>
                        <a href="/app/password-reset" class="text-sm text-amber-500 hover:text-amber-400">
                            Forgot password?
                        </a>
                    </div>
                    <input
                        wire:model="password"
                        type="password"
                        id="password"
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                        placeholder="••••••••"
                        required
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center">
                    <input
                        wire:model="remember"
                        type="checkbox"
                        id="remember"
                        class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-amber-500 focus:ring-amber-500 focus:ring-offset-gray-900"
                    >
                    <label for="remember" class="ml-2 text-sm text-gray-400">
                        Remember me
                    </label>
                </div>

                {{-- Submit Button --}}
                <button
                    type="submit"
                    class="w-full px-4 py-3 bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold rounded-lg transition-colors duration-200 flex items-center justify-center"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-75 cursor-wait"
                >
                    <span wire:loading.remove>Sign in</span>
                    <span wire:loading class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Signing in...
                    </span>
                </button>
            </form>

            <p class="mt-6 text-center text-gray-500 text-sm">
                Free to play. No donations required.
            </p>
        </div>
    </div>
</section>
