<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = ['code', 'discount', 'type', 'expires_at'];

    protected $casts = [
        'expires_at' => 'datetime', 
    ];

    public function isValid()
    {
        return !$this->expires_at || $this->expires_at->isFuture();
    }
}
