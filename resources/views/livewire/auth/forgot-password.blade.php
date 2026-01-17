@section('title', 'Reset Your Password | XileRO Account Recovery')
@section('description', 'Forgot your XileRO password? No problem! Enter your email to receive a password reset link and regain access to your account.')
@section('robots', 'noindex, follow')

<section class="bg-clash-bg min-h-screen flex items-center justify-center pt-24 pb-12 px-4">
    <div class="w-full max-w-md">
        {{-- Card --}}
        <div class="bg-gray-900/80 rounded-xl border border-gray-800 shadow-lg overflow-hidden">
            <div class="p-6">
                {{-- Header --}}
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-white mb-1">Reset Password</h1>
                    <p class="text-gray-400 text-sm">
                        Enter your email and we'll send you a reset link
                    </p>
                </div>

                @if($emailSent)
                    {{-- Success Message --}}
                    <div class="p-4 bg-green-900/30 border border-green-700 rounded-lg mb-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-green-400 text-sm">
                                If an account exists with that email, you'll receive a password reset link shortly.
                            </p>
                        </div>
                    </div>

                    <a href="{{ route('login') }}" class="block w-full px-4 py-3 bg-gradient-to-r from-xilero-gold to-amber-600 hover:from-amber-500 hover:to-amber-600 text-gray-900 font-bold rounded-lg shadow-md transition-all duration-200 text-center">
                        Return to Sign In
                    </a>
                @else
                    <form wire:submit="sendResetLink" class="space-y-4">
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

                        {{-- Submit Button --}}
                        <button
                            type="submit"
                            class="w-full px-4 py-3 bg-gradient-to-r from-xilero-gold to-amber-600 hover:from-amber-500 hover:to-amber-600 text-gray-900 font-bold rounded-lg shadow-md transition-all duration-200"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-75 cursor-wait"
                        >
                            <span wire:loading.remove>Send Reset Link</span>
                            <span wire:loading>Sending...</span>
                        </button>
                    </form>
                @endif

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
