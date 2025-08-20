<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'patcher_notice',
        'article_content',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
