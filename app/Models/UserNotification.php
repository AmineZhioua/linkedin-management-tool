<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $fillable = [
        'user_id',
        'linkedin_user_id',
        'campaign_id',
        'event_name',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'campaign_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function linkedinUser()
    {
        return $this->belongsTo(LinkedinUser::class);
    }

    public function campaign()
    {
        return $this->belongsTo(LinkedinCampaign::class);
    }
}
