<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'completed',
        'has_email',
        'has_deadline',
        'deadline',
        'transferred'
    ];

    protected $casts = [
        'completed' => 'boolean',
        'has_email' => 'boolean',
        'has_deadline' => 'boolean',
        'transferred' => 'boolean',
        'deadline' => 'date'
    ];
}
