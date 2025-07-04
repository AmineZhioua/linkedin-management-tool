<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledLinkedinPost extends Model
{
    use HasFactory;

    protected $table = 'scheduled_linkedin_posts';

    protected $fillable = [
        'user_id',
        'linkedin_user_id',
        'campaign_id',
        'job_id',
        'type',
        'content',
        'scheduled_time',
        'status',
        'post_urn',
        'error',
    ];

    // Define the relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function linkedinUser()
    {
        return $this->belongsTo(LinkedinUser::class, 'linkedin_user_id');
    }

    public function campaign()
    {
        return $this->belongsTo(LinkedinCampaign::class, 'campaign_id');
    }
    // Optionally, define mutators, accessors, or casts if needed
    protected $casts = [
        'scheduled_time' => 'datetime',
    ];
}
