<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkedinCampaign extends Model
{
    use HasFactory;

    protected $table = 'linkedin_campaigns';

    protected $fillable = [
        'user_id',
        'linkedin_user_id',
        'name',
        'description',
        'target_audience',
        'frequency_per_day',
        'color',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function linkedinUser()
    {
        return $this->belongsTo(LinkedinUser::class, 'linkedin_user_id');
    }

    public function posts()
    {
        return $this->hasMany(ScheduledLinkedinPost::class, 'campaign_id');
    }
}