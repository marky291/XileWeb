<div>
    <section id="myaccount" class="bg-clash-bg relative overflow-hidden py-12 px-24">
        <div class="max-w-screen-xl w-full mx-auto lg:px-0 px-5">
            <h2>Welcome {{ Str::ucfirst(auth()->user()->name) }}!</h2>

            <div class="grid grid-cols-4 gap-6">
                @forelse($this->characters() as $character)
                    <div class="h-50 w-25 border rounded p-4">
                        <p class="text-gray-100 text-center">{{ $character->name }}</p>
                    </div>
                @empty
                    <div class="col-span-4">
                        <p class="text-gray-400">No characters found. Please create a game account first.</p>
                    </div>
                @endforelse
            </div>

{{--            {{ auth()->user()->userLogins()->pluck('login_account_id')->first() }}--}}

{{--            <p class="text-white">{{ \App\Ragnarok\Login::find(auth()->user()->userLogins()->pluck('login_account_id')->first())->chars }}</p>--}}

        </div>
    </section>
</div>
