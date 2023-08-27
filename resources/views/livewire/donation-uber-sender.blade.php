<section id="important-links" class="bg-black mx-auto px-5 pt-4 pb-6 md:pt-4 lg:pt-4">
    <div class="max-w-screen-xl w-full mx-auto bg-gray-800 rounded p-5">
        <div class="">
            <div class="grid grid-cols-2 mb-8">
                <h2 class="mb-0 text-xl font-bold">Send Ubers</h2>
            </div>
            @if($this->isSent)
                <h2>You have sent {{ $this->uber_amount }} ubers to {{ $this->username }}</h2>
            @else
                <form wire:submit autocomplete="off">
                    <div class="space-y-5">
                        <div class="mb-2">
                            <div class="w-full">
                                <label class="block mb-2 text-xs font-bold tracking-wide text-gray-200 uppercase"
                                       for="grid-username">
                                    Username
                                </label>
                                <input wire:model.live="username"
                                       class="block w-full px-4 py-3 mb-3 leading-tight text-gray-100 bg-gray-800 border border-gray-900 rounded appearance-none focus:outline-none focus:bg-gray-600 focus:border-gray-500 @error('username') border-red-500 @enderror"
                                       id="grid-username" value="{{ old('username') }}" type="text" placeholder="Username"
                                       autocomplete="off">
                                @error('username')
                                <p class="mt-4 text-xs italic text-red-500">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="w-full">
                                <label class="block mb-2 text-xs font-bold tracking-wide text-gray-200 uppercase"
                                       for="grid-username">
                                    Uber Amount
                                </label>
                                <input wire:model.live="uber_amount"
                                       class="block w-full px-4 py-3 mb-3 leading-tight text-gray-100 bg-gray-800 border border-gray-900 rounded appearance-none focus:outline-none focus:bg-gray-600 focus:border-gray-500 @error('uber_amount') border-red-500 @enderror"
                                       id="grid-uber_amount" value="{{ old('uber_amount') }}" type="text" placeholder="0"
                                       autocomplete="off">
                                @error('uber_amount')
                                <p class="mt-4 text-xs italic text-red-500">
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-start mt-6">
                        <div wire:loading>
                            <button class="btn w-auto py-12 text-left mt-4 xilero-button bg-gray-500">
                                <span>Sending...</span>
                            </button>
                        </div>
                        <button wire:click="send" class="btn w-auto py-12 text-left mt-4 xilero-button">
                            <span>Send</span>
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</section>
