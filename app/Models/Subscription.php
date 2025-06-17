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
        'linkedin',
        'whatsapp',
        'discount',
        'boost_likes',
        'available_posts',
        'boost_comments',
    ];
}