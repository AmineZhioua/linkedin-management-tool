<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = ['code', 'discount', 'type', 'expires_at'];

    // Ensure that expires_at is cast to a Carbon instance
    protected $casts = [
        'expires_at' => 'datetime',  // This will automatically cast 'expires_at' to a Carbon instance
    ];

    public function isValid()
    {
        // Now you can safely call isFuture() on expires_at, which is a Carbon instance
        return !$this->expires_at || $this->expires_at->isFuture();
    }
}
