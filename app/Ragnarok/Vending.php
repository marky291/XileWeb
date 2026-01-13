<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $account_id
 * @property int $char_id
 * @property string $sex
 * @property string $map
 * @property int $x
 * @property int $y
 * @property string $title
 * @property string $body_direction
 * @property string $head_direction
 * @property string $sit
 * @property int $autotrade
 * @property-read Login $login
 * @property-read Char $char
 * @property-read \Illuminate\Database\Eloquent\Collection<int, VendingItems> $items
 */
class Vending extends RagnarokModel
{
    use HasFactory;

    protected $connection = 'main';

    protected $table = 'vendings';

    protected $primaryKey = 'id';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'account_id',
        'char_id',
        'sex',
        'map',
        'x',
        'y',
        'title',
        'body_direction',
        'head_direction',
        'sit',
        'autotrade',
    ];

    public function login(): BelongsTo
    {
        return $this->belongsTo(Login::class, 'account_id', 'account_id');
    }

    public function char(): BelongsTo
    {
        return $this->belongsTo(Char::class, 'char_id', 'char_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(VendingItems::class, 'vending_id', 'id');
    }
}
