<x-mail::message>
# Hey {{ $notifiable->name }}!

Thank you so much for your generous donation of **${{ $amount }}**! Your support means the world to us and helps keep XileRO running strong.

We've added **{{ $totalUbers }} Ubers** to your account.

**Your new Uber balance: {{ $newBalance }} Ubers**

Payment received via: {{ $paymentMethodName }}

@if(!empty($bonusRewards['xilero']) || !empty($bonusRewards['xileretro']))
---

## Bonus Reward Items!

Your donation qualified for the following bonus items:

@if(!empty($bonusRewards['xilero']))
### XileRO Bonus Items

<x-mail::table>
| Item | Qty | Refine |
|:-----|:---:|:------:|
@foreach($bonusRewards['xilero'] as $reward)
| <img src="{{ $reward['icon_url'] }}" width="24" height="24" style="vertical-align: middle;"> {{ $reward['item_name'] }} ({{ $reward['item_id'] }}) | {{ $reward['quantity'] }} | {{ $reward['refine_level'] > 0 ? '+' . $reward['refine_level'] : '-' }} |
@endforeach
</x-mail::table>
@endif

@if(!empty($bonusRewards['xileretro']))
### XileRetro Bonus Items

<x-mail::table>
| Item | Qty | Refine |
|:-----|:---:|:------:|
@foreach($bonusRewards['xileretro'] as $reward)
| <img src="{{ $reward['icon_url'] }}" width="24" height="24" style="vertical-align: middle;"> {{ $reward['item_name'] }} ({{ $reward['item_id'] }}) | {{ $reward['quantity'] }} | {{ $reward['refine_level'] > 0 ? '+' . $reward['refine_level'] : '-' }} |
@endforeach
</x-mail::table>
@endif

**How to claim:** Visit your Dashboard on our website to claim your items to any character on the appropriate server.

<x-mail::button :url="$claimUrl">
Claim Your Bonus Items
</x-mail::button>
@else
<x-mail::button :url="$shopUrl">
Visit the Uber Shop
</x-mail::button>
@endif

Ready to spend your Ubers? Head over to the Uber Shop to browse exclusive gear, costumes, and items!

Thank you again for being an awesome part of our community. See you in-game!

With gratitude,<br>
The XileRO Team
</x-mail::message>
