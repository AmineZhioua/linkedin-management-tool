<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'monthly_price',
        'yearly_price',
        'description',
        'features',
        'linkedin',
        'whatsapp',
        'discount',
    ];

    protected $casts = [
        'features' => 'array', // Convert JSON field to array automatically
    ];
}
