<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LinkedinUser extends Model
{
    protected $fillable = [
        'user_id',
        'linkedin_id',
        'linkedin_firstname',
        'linkedin_lastname',
        'linkedin_picture',
        'linkedin_token',
        'linkedin_refresh_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
