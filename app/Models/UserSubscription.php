<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $table = 'user_subscriptions';

    protected $fillable = [
        'user_id',
        'subscription_id',
        'pricing_mode',
        'date_expiration',
        'boost_likes',
        'available_posts',
    ];

    protected $casts = [
        'date_expiration' => 'date',
    ];

    /**
     * Get the user that owns this subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription that this user subscription belongs to.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}