<li href="/docs/9.x/starter-kits#laravel-breeze" class="flex p-4 border border-gray-200 border-opacity-60">
    <div class="relative shrink-0 bg-breeze flex items-center justify-center w-12 h-12 rounded-lg overflow-hidden">
        <span class="absolute w-full h-full inset-0 bg-gradient-to-b from-[rgba(255,255,255,.2)] to-[rgba(255,255,255,0)]"></span>
        <img src="/images/donations/{{ $image }}.png" alt="Icon" class="relative w-7 h-7">
    </div>
    <div class="ml-4 leading-5">
        <div>{{ $name }}</div>
        <div class="mt-1 text-sm text-gray-700">{{ $slot }}</div>
        <div class="mt-1 text-sm text-rose-800 font-bold">{{ $cost }} Ubers</div>
        @if(isset($set))
            <div class="mt-1 text-sm text-blue-800 font-bold">Item Set Bonus</div>
        @endif
    </div>
</li>