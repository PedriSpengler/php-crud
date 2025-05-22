<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'body',
        'expires_at',
        'completed',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'expires_at' => 'datetime',
        'completed' => 'boolean',
    ];
}
