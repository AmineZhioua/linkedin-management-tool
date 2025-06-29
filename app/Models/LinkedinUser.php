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
        'expire_at',
    ];

    protected $dates = ['expire_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(ScheduledLinkedinPost::class, 'linkedin_user_id');
    }

    public function campaigns()
    {
        return $this->hasMany(LinkedinCampaign::class, 'linkedin_user_id');
    }
}