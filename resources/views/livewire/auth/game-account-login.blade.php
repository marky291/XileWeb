@section('title', 'Sign In - XileRO')

<section class="bg-clash-bg min-h-screen flex items-center justify-center pt-24 pb-12 px-4">
    <div class="w-full max-w-md">
        {{-- Card --}}
        <div class="bg-gray-900/80 rounded-xl border border-gray-800 shadow-lg overflow-hidden">
            <div class="p-6">
                {{-- Header --}}
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-white mb-1">Welcome Back</h1>
                    <p class="text-gray-400 text-sm">
                        Sign in to manage your game accounts
                    </p>
                </div>

                <form wire:submit="authenticate" class="space-y-4">
                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1">
                            Email Address
                        </label>
                        <input
                            wire:model="email"
                            type="email"
                            id="email"
                            class="w-full px-3 py-2.5 bg-gray-800/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-xilero-gold/50 focus:border-xilero-gold transition-colors"
                            placeholder="you@example.com"
                            required
                            autofocus
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-medium text-gray-300">
                                Password
                            </label>
                            <a href="/app/password-reset" class="text-xs text-xilero-gold hover:text-amber-400">
                                Forgot password?
                            </a>
                        </div>
                        <input
                            wire:model="password"
                            type="password"
                            id="password"
                            class="w-full px-3 py-2.5 bg-gray-800/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-xilero-gold/50 focus:border-xilero-gold transition-colors"
                            placeholder="Your password"
                            required
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center">
                        <input
                            wire:model="remember"
                            type="checkbox"
                            id="remember"
                            class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-xilero-gold focus:ring-xilero-gold focus:ring-offset-gray-900"
                        >
                        <label for="remember" class="ml-2 text-sm text-gray-400">
                            Keep me signed in
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        class="w-full px-4 py-3 bg-gradient-to-r from-xilero-gold to-amber-600 hover:from-amber-500 hover:to-amber-600 text-gray-900 font-bold rounded-lg shadow-md transition-all duration-200"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-75 cursor-wait"
                    >
                        <span wire:loading.remove>Sign In</span>
                        <span wire:loading>Signing in...</span>
                    </button>
                </form>

                {{-- Divider --}}
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-700"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-gray-900 text-gray-500">Or continue with</span>
                    </div>
                </div>

                {{-- Discord Login Button --}}
                <a href="{{ route('auth.discord.redirect') }}"
                   class="flex items-center justify-center gap-3 w-full px-4 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/>
                    </svg>
                    <span>Continue with Discord</span>
                </a>

                {{-- Register Link --}}
                <div class="mt-4 text-center">
                    <span class="text-gray-500 text-sm">New to XileRO?</span>
                    <a href="{{ route('register') }}" class="text-xilero-gold hover:text-amber-400 text-sm font-medium ml-1">
                        Create account
                    </a>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-3 bg-gray-800/30 border-t border-gray-800">
                <p class="text-center text-gray-500 text-xs">
                    Free to play forever. No donations required.
                </p>
            </div>
        </div>

        {{-- Info --}}
        <p class="mt-4 text-center text-gray-500 text-xs">
            Your master account gives you access to create up to
            <span class="text-xilero-gold font-medium">6 game accounts</span>
        </p>
    </div>
</section>
