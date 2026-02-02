<div>
    {{-- Section Header --}}
    <div class="flex items-end justify-between mb-10">
        <div>
            <h2 class="text-3xl font-bold text-gray-100 mb-2">Download XileRetro</h2>
            @if (config('xilero.auth.enabled'))
                <p class="text-gray-400">Create a free account to access all downloads.</p>
            @else
                <p class="text-gray-400">Choose your platform and start playing.</p>
            @endif
        </div>
    </div>

    @if (!config('xilero.auth.enabled'))
        {{-- Auth Disabled: Show downloads directly with migration notice --}}
        <div class="p-4 rounded-lg bg-orange-500/10 border border-orange-500/20 mb-6">
            <p class="text-orange-200 text-sm leading-relaxed">
                <i class="fas fa-info-circle mr-2"></i>
                {{ config('xilero.auth.maintenance_message') }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Windows Card --}}
            <div class="card-glow-wrapper group transition-all duration-300 hover:-translate-y-1">
                <div class="card-glow-inner p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-lg bg-blue-500/10 border border-blue-500/20 flex items-center justify-center">
                            <i class="fa fa-windows text-2xl text-blue-400"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-100">Windows</h3>
                            <p class="text-gray-400 text-sm">Full client download</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach(\App\Models\Download::full()->get() as $download)
                            <a href="{{ $download->download_url }}" target="_blank" rel="noopener" class="flex items-center justify-between p-3 rounded-lg bg-gray-800/50 border border-gray-700/50 hover:border-blue-500/30 hover:bg-gray-800 transition-colors no-underline group/btn">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-download text-gray-500 group-hover/btn:text-blue-400 transition-colors"></i>
                                    <span class="text-gray-300 group-hover/btn:text-gray-100 transition-colors">{{ $download->name }}</span>
                                </div>
                                <span class="text-xs text-gray-500">3GB</span>
                            </a>
                        @endforeach
                    </div>

                    <p class="mt-5 text-gray-500 text-xs flex items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        Extract and run the patcher to update
                    </p>
                </div>
            </div>

            {{-- Android Card --}}
            <div class="card-glow-wrapper group transition-all duration-300 hover:-translate-y-1">
                <div class="card-glow-inner p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-lg bg-green-500/10 border border-green-500/20 flex items-center justify-center">
                            <i class="fa fa-android text-2xl text-green-400"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-100">Android</h3>
                            <p class="text-gray-400 text-sm">Play on the go</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach(\App\Models\Download::android()->get() as $download)
                            <a href="{{ $download->download_url }}" target="_blank" rel="noopener" class="flex items-center justify-between p-3 rounded-lg bg-gray-800/50 border border-gray-700/50 hover:border-green-500/30 hover:bg-gray-800 transition-colors no-underline group/btn">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-download text-gray-500 group-hover/btn:text-green-400 transition-colors"></i>
                                    <span class="text-gray-300 group-hover/btn:text-gray-100 transition-colors">{{ $download->name }}</span>
                                </div>
                                <span class="text-xs text-gray-500">3MB</span>
                            </a>
                        @endforeach
                    </div>

                    <p class="mt-5 text-gray-500 text-xs flex items-center gap-2">
                        <i class="fas fa-shield-alt"></i>
                        Supports Gepard protection & auto-updates
                    </p>
                </div>
            </div>
        </div>
    @else
        {{-- Auth Enabled: Show login/register prompt --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Sign In Card --}}
            <div class="card-glow-wrapper group transition-all duration-300 hover:-translate-y-1">
                <div class="card-glow-inner p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center">
                            <i class="fas fa-user-plus text-2xl text-amber-400"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-100">Get Started</h3>
                            <p class="text-gray-400 text-sm">Create account or sign in</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('register') }}" class="flex items-center justify-between p-3 rounded-lg bg-amber-500/10 border border-amber-500/30 hover:bg-amber-500/20 transition-colors no-underline group/btn">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-rocket text-amber-500"></i>
                                <span class="text-amber-100 font-medium">Register Now</span>
                            </div>
                            <i class="fas fa-arrow-right text-amber-500 text-sm group-hover/btn:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="{{ route('login') }}" class="flex items-center justify-between p-3 rounded-lg bg-gray-800/50 border border-gray-700/50 hover:border-gray-600 hover:bg-gray-800 transition-colors no-underline group/btn">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-sign-in-alt text-gray-500 group-hover/btn:text-gray-300 transition-colors"></i>
                                <span class="text-gray-300 group-hover/btn:text-gray-100 transition-colors">Log In</span>
                            </div>
                            <i class="fas fa-arrow-right text-gray-500 text-sm group-hover/btn:translate-x-1 transition-transform"></i>
                        </a>
                    </div>

                    <p class="mt-5 text-gray-500 text-xs flex items-center gap-2">
                        <i class="fas fa-clock"></i>
                        Registration takes less than 30 seconds
                    </p>
                </div>
            </div>

            {{-- Download Preview Card (locked) --}}
            <div class="card-glow-wrapper opacity-50 relative">
                <div class="card-glow-inner p-6">
                    {{-- Lock overlay --}}
                    <div class="absolute inset-0 flex items-center justify-center z-10 rounded-md bg-gray-900/60">
                        <div class="bg-gray-900 border border-gray-700 rounded-lg px-5 py-2.5 flex items-center gap-3 shadow-xl">
                            <i class="fas fa-lock text-amber-500"></i>
                            <span class="text-gray-200 font-medium text-sm">Sign in to unlock</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-14 h-14 rounded-lg bg-gray-700/50 border border-gray-600/30 flex items-center justify-center">
                            <i class="fas fa-download text-2xl text-gray-500"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-500">Downloads</h3>
                            <p class="text-gray-600 text-sm">Windows & Android</p>
                        </div>
                    </div>

                    <div class="space-y-3 pointer-events-none select-none">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-gray-800/30 border border-gray-700/20">
                            <div class="flex items-center gap-3">
                                <i class="fa fa-windows text-gray-600"></i>
                                <span class="text-gray-500">Full Client</span>
                            </div>
                            <span class="text-xs text-gray-600">3GB</span>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-gray-800/30 border border-gray-700/20">
                            <div class="flex items-center gap-3">
                                <i class="fa fa-android text-gray-600"></i>
                                <span class="text-gray-500">Android APK</span>
                            </div>
                            <span class="text-xs text-gray-600">3MB</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
