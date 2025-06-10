<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = ['message', 'start_at', 'end_at'];
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];
}