<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendingItems extends RagnarokModel
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'vending_id';

    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'main';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vending_items';

    public function vending()
    {
        return $this->belongsTo(Vending::class, 'vending_id', 'id');
    }
}
