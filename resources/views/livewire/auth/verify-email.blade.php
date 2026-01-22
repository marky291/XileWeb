@section('title', 'Verify Email - XileRO')

<section class="bg-clash-bg min-h-screen flex items-center justify-center pt-24 pb-12 px-4">
    <div class="w-full max-w-md">
        {{-- Card --}}
        <div class="bg-gray-900/80 rounded-xl border border-gray-800 shadow-lg overflow-hidden">
            <div class="p-6">
                {{-- Header --}}
                <div class="text-center mb-6">
                    <div class="w-16 h-16 mx-auto mb-4 bg-amber-500/20 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-xilero-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-1">Verify Your Email</h1>
                    <p class="text-gray-400 text-sm">
                        Thanks for signing up! Before getting started, please verify your email address by clicking the link we sent to:
                    </p>
                    <p class="text-xilero-gold font-medium mt-2">
                        {{ auth()->user()->email }}
                    </p>
                </div>

                {{-- Info Box --}}
                <div class="p-4 bg-blue-900/20 border border-blue-800/50 rounded-lg mb-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-gray-300">
                            <p class="mb-1">The email may take up to <strong class="text-white">5 minutes</strong> to arrive.</p>
                            <p>Please check your <strong class="text-white">spam or junk folder</strong> if you don't see it in your inbox.</p>
                        </div>
                    </div>
                </div>

                @if (session('status') === 'verification-link-sent')
                    {{-- Success Message --}}
                    <div class="p-4 bg-green-900/30 border border-green-700 rounded-lg mb-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-green-400 text-sm">
                                A new verification link has been sent to your email address.
                            </p>
                        </div>
                    </div>
                @endif

                @if (session('throttle'))
                    {{-- Throttle Message --}}
                    <div class="p-4 bg-amber-900/30 border border-amber-700 rounded-lg mb-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-amber-400 text-sm">
                                Please wait {{ session('throttle') }} seconds before requesting another email.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Resend Button --}}
                <button
                    wire:click="sendVerification"
                    class="w-full px-4 py-3 bg-gradient-to-r from-xilero-gold to-amber-600 hover:from-amber-500 hover:to-amber-600 text-gray-900 font-bold rounded-lg shadow-md transition-all duration-200"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-75 cursor-wait"
                >
                    <span wire:loading.remove>Resend Verification Email</span>
                    <span wire:loading>Sending...</span>
                </button>

                {{-- Logout Link --}}
                <div class="mt-4 text-center">
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xilero-gold hover:text-amber-400 text-sm font-medium">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
