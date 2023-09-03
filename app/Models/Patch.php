<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patch extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'type',
        'patch_name'
    ];
}
