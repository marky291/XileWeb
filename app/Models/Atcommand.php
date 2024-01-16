<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atcommand extends Model
{
    use HasFactory;

    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'ragnarok_logs';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'atcommandlog';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'atcommand_id';
}
