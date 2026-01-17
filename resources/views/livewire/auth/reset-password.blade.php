@section('title', 'Set New Password - XileRO')

<section class="bg-clash-bg min-h-screen flex items-center justify-center pt-24 pb-12 px-4">
    <div class="w-full max-w-md">
        {{-- Card --}}
        <div class="bg-gray-900/80 rounded-xl border border-gray-800 shadow-lg overflow-hidden">
            <div class="p-6">
                {{-- Header --}}
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-white mb-1">Set New Password</h1>
                    <p class="text-gray-400 text-sm">
                        Enter your new password below
                    </p>
                </div>

                <form wire:submit="resetPassword" class="space-y-4">
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
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-300 mb-1">
                            New Password
                        </label>
                        <input
                            wire:model="password"
                            type="password"
                            id="password"
                            class="w-full px-3 py-2.5 bg-gray-800/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-xilero-gold/50 focus:border-xilero-gold transition-colors"
                            placeholder="Minimum 8 characters"
                            required
                            autofocus
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1">
                            Confirm New Password
                        </label>
                        <input
                            wire:model="password_confirmation"
                            type="password"
                            id="password_confirmation"
                            class="w-full px-3 py-2.5 bg-gray-800/50 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-xilero-gold/50 focus:border-xilero-gold transition-colors"
                            placeholder="Confirm your password"
                            required
                        >
                    </div>

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        class="w-full px-4 py-3 bg-gradient-to-r from-xilero-gold to-amber-600 hover:from-amber-500 hover:to-amber-600 text-gray-900 font-bold rounded-lg shadow-md transition-all duration-200"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-75 cursor-wait"
                    >
                        <span wire:loading.remove>Reset Password</span>
                        <span wire:loading>Resetting...</span>
                    </button>
                </form>

                {{-- Back to Login Link --}}
                <div class="mt-4 text-center">
                    <a href="{{ route('login') }}" class="text-xilero-gold hover:text-amber-400 text-sm font-medium">
                        Back to Sign In
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
