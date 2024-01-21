<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapreg extends Model
{
    use HasFactory;

    protected $connection = 'main';

    protected $table = 'mapreg';

    public $timestamps = false;
    protected $fillable = [
        "varname",
        "index",
        "value"
    ];
}
