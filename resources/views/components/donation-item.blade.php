<li>
    <a id="{{ $name }}" href="https://wiki.xileretro.net/index.php?title=Donation" class="donation-item flex p-4 border rounded border-gray-200 border-opacity-60 hover:border-rose-200 hover:cursor-pointer hover:shadow-md">
        <div class="relative shrink-0 bg-breeze flex items-center justify-center w-16 h-20 rounded-lg overflow-hidden">
            <span class="absolute w-full h-full inset-0 bg-gradient-to-b from-[rgba(255,255,255,.2)] to-[rgba(255,255,255,0)]"></span>
            <img src="/images/donations/{{ $image }}" alt="Icon" class="relative w-12 h-20">
        </div>
        <div class="ml-4 leading-5">
            <div>{{ $name }}</div>
            <div class="mt-1 text-sm text-gray-700">{{ $slot }}</div>
            <div class="mt-1 text-sm text-rose-800 font-bold">{{ $cost }} Ubers</div>
            @if(isset($set))
                <div class="mt-1 text-sm text-blue-800 font-bold">Click to view Item Set Bonus</div>
            @endif
        </div>
    </a>
</li>