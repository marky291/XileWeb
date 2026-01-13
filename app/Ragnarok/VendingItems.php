<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $vending_id
 * @property int $index
 * @property int $cartinventory_id
 * @property int $amount
 * @property int $price
 * @property-read Vending $vending
 */
class VendingItems extends RagnarokModel
{
    use HasFactory;

    protected $connection = 'main';

    protected $table = 'vending_items';

    protected $primaryKey = ['vending_id', 'index'];

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'vending_id',
        'index',
        'cartinventory_id',
        'amount',
        'price',
    ];

    public function vending(): BelongsTo
    {
        return $this->belongsTo(Vending::class, 'vending_id', 'id');
    }
}
