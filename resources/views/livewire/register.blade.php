        <div class="grid grid-cols-2 gap-20 ">
            <div class="col-span-2 lg:col-span-1 mb-16">
                @guest
                <h2 class="mt-0 mb-2 text-2xl font-bold text-gray-100"><span class="mr-2">1.</span> Register an Account.</h2>
                @if ($error)
                 <div class="rounded-md bg-red-50 p-4 mb-5">
                    <div class="flex">
                      <div class="flex-shrink-0">
                        <!-- Heroicon name: x-circle -->
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                      </div>
                      <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 mb-0">
                            {{ $error }}
                        </h3>
                      </div>
                    </div>
                  </div>
                @else
                    <p class="mb-12 text-amber-500">Let's get you ready to login and play.</h4>
                @endif

                <form>
                    @csrf
                    <div class="flex flex-wrap mb-2">
                        <div class="w-full">
                            <label class="block mb-2 text-xs font-bold tracking-wide text-gray-300 uppercase" for="grid-username">
                                Username
                            </label>
                            <input wire:model="username" class="block w-full px-4 py-3 mb-3 leading-tight text-gray-100 bg-gray-800 border border-gray-900 rounded appearance-none focus:outline-none focus:bg-gray-600 focus:border-gray-500 @error('username') border-red-500 @enderror" id="grid-username" value="{{ old('username') }}" type="text" placeholder="username" required autocomplete="name">
                            @error('username')
                                <p class="mt-4 text-xs italic text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex flex-wrap mb-2">
                        <div class="w-full">
                            <label class="block mb-2 text-xs font-bold tracking-wide text-gray-300 uppercase" for="grid-email">
                                Email Address
                            </label>
                            <input wire:model="email" class="block w-full px-4 py-3 mb-3 leading-tight text-gray-100 bg-gray-800 border border-gray-900 rounded appearance-none focus:outline-none focus:bg-gray-600 focus:border-gray-500 @error('email') border-red-500 @enderror" id="grid-email" value="{{ old('email') }}" type="email" placeholder="account@xileretro.net" required autocomplete="email">
                            @error('email')
                                <p class="mt-4 text-xs italic text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex flex-wrap mb-2">
                        <div class="w-full">
                            <label class="block mb-2 text-xs font-bold tracking-wide text-gray-300 uppercase" for="grid-password">
                                Password
                            </label>
                            <input wire:model="password" class="block w-full px-4 py-3 mb-3 leading-tight text-gray-100 bg-gray-800 border border-gray-900 rounded appearance-none focus:outline-none focus:bg-gray-600 focus:border-gray-500 @error('password') border-red-500 @enderror" id="grid-password" type="password" placeholder="******************" required autocomplete="new-password">
                            @error('password')
                                <p class="mt-4 text-xs italic text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex flex-wrap mb-2">
                        <div class="w-full">
                            <label class="block mb-2 text-xs font-bold tracking-wide text-gray-300 uppercase" for="grid-password-confirm">
                                Confirm Password
                            </label>
                            <input wire:model="password_confirmation" class="block w-full px-4 py-3 mb-3 leading-tight text-gray-100 bg-gray-800 border border-gray-900 rounded appearance-none focus:outline-none focus:bg-gray-600 focus:border-gray-500" id="grid-password-confirm" type="password" placeholder="******************" required autocomplete="new-password">
                        </div>
                    </div>
					<div class="flex justify-start mt-6">
                        <button wire:click.prevent="register" class="col-span-1 btn py-4 btn-primary bg-amber-500 hover:bg-amber-300 text-gray-900">
                            <span class="click">Register Account</span>
                        </button>
                    </div>
                </form>
                @else
                <div class="pr-28 prose text-gray-300">
                    <div class="text-gray-300">
                        <h2 class="mt-0 text-gray-100">Registration Completed!</h4>
                        <p>You have created the following account.</p>
                        <ul>
                            <li><span class="font-bold">Username:</span> <span class="text-amber-300">{{ auth()->user()->userid }}</span></li>
                            <li><span class="font-bold">Password:</span> <span class="text-amber-300">{{ $this->password }}</span></li>
                            <li><span class="font-bold">Email:</span> <span class="text-amber-300">{{ auth()->user()->email }}</span></li>
                        </ul>
                        <p class="py-3">You can now login with the credentials created, if at any point you wish to change password please use <span class="font-medium text-amber-300">@myaccount</span> in game.</p>
                    </div>
                    <hr>
                    <h2 class="text-gray-100">Get a Headstart</h4>
                    <p>If you are new to XileRO or would like a refresher, we highly recommend checking out the <a class="text-amber-300 hover:text-amber-100" href="http://wiki.xileretro.net/index.php?title=Newbie_Center" target="_blank">Newbie Center Guide</a> for an awesome head start!</p>
                    <hr>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        {{ csrf_field() }}
                        <button class="btn btn-secondary bg-amber-500 hover:bg-amber-300 text-gray-900" action="submit">Logout from Website</button>
                    </form>

                </div>
                @endguest
            </div>
            <div class="col-span-2 lg:col-span-1">
                <h2 class="mt-0 mb-2 text-2xl font-bold text-gray-100"><span class="mr-2">2.</span> Download Client.</h2>
                <p class="text-amber-500 mb-12">Download and install with Full Installer.</h3>
				<div class="grid grid-cols-5">
					<div class="col-span-1 hidden md:block">
                        <i class="fas fa-file-archive step2-icon text-gray-300"></i>
					</div>
					<div class="col-span-5 md:col-span-4">
                        @foreach(config('downloads.full') as $item)
                            <a class="no-underline" href="{{ $item['link'] }}" target="_blank">
                                <button class="truncate ... flex items-center w-full mb-4 text-left btn py-4 {{ $item['bttn'] }}">
                                    {{ $item['name'] }}
                                </button>
                            </a>
                        @endforeach
					</div>
				</div>

                <div class="bg-amber-700 bg-amber-300 bg-gray-400 hover:bg-amber-100 hover:bg-gray-300 text-gray-900 hover:bg-white"></div>

				<p class="my-12 text-amber-500">Download and install with Lite Installer.</p>
				<div class="grid grid-cols-5">
					<div class="col-span-1 hidden md:block">
                    <i class="fas fa-compact-disc step2-icon text-gray-300"></i>
					</div>
					<div class="col-span-5 md:col-span-4">
                        @foreach(config('downloads.lite') as $item)
                            <a class="no-underline" href="{{ $item['link'] }}" target="_blank">
                                <button class="truncate ... flex items-center w-full mb-4 py-4 text-left btn {{ $item['bttn'] }}">
                                    {{ $item['name'] }}
                                </button>
                            </a>
                        @endforeach
					</div>
				</div>

            </div>
        </div>