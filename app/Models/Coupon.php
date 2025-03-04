<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'discount', 'type', 'expires_at'];

    public function isValid()
    {
        return !$this->expires_at || $this->expires_at->isFuture();
    }
}