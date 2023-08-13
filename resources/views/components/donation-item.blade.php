<li>
    <a id="{{ Str::slug($name) }}" title="Uber Shop Item {{ $name }}" aria-label="Uber Shop Item {{ $name }}" href="https://wiki.xileretro.net/index.php?title=Donation" class="donation-item flex p-4 border rounded border-black bg-gray-900 border-opacity-60 hover:border-amber-500 hover:cursor-pointer hover:shadow-md">
        <div class="relative shrink-0 bg-breeze flex items-center justify-center rounded-lg overflow-hidden" style="height:100px; width=75px;">
            {{-- <span class="absolute w-full h-full inset-0 bg-gradient-to-b from-[rgba(255,255,255,.2)] to-[rgba(255,255,255,0)]"></span> --}}
            <img src="/images/donations/{{ $image }}" alt="{{ $name }} Item" class="relative" width="75" height="100">
        </div>
        <div class="ml-4 leading-5">
            <div class="text-gray-100">{{ $name }}</div>
            <div class="mt-1 text-sm text-gray-300">{{ $slot }}</div>
            <div class="mt-1 text-sm text-amber-500 font-bold">{{ $cost }} Ubers</div>
            @if(isset($set))
                <div class="mt-1 text-sm text-amber-300 font-bold">Click to view Item Set Bonus</div>
            @endif
        </div>
    </a>
</li>
