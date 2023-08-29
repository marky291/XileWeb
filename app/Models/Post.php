<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'blurb',
        'slug',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
