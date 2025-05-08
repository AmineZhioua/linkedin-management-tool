<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'email_verified_at',
        'role',
        'post_perm',
        'boost_perm',
        'image',
        'last_activity',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'post_perm' => 'boolean',
            'boost_perm' => 'boolean',
            'last_activity' => 'datetime',
        ];
    }

    /**
     * The relationships to eager load on every query.
     *
     * @var array
     */
    protected $with = ['linkedinUser'];

    /**
     * Get the LinkedIn user record associated with the user.
     */
    public function linkedinUser()
    {
        return $this->hasOne(LinkedinUser::class);
    }

    /**
     * Scope a query to filter users by name or email.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, $search = null)
    {
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
        }

        return $query;
    }

    /**
     * Scope a query to get users active in the current hour.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActiveInCurrentHour($query)
    {
        return $query->where('last_activity', '>=', now()->subHour());
    }

    /**
     * Get cached users with optional search.
     *
     * @param string|null $search
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public static function getCachedUsers($search = null)
    {
        $cacheKey = 'users_' . md5($search ?? 'all') . '_page_' . request()->get('page', 1);

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($search) {
            return static::filter($search)->paginate(10);
        });
    }

    /**
     * Get the count of active users in the current hour, cached.
     *
     * @return int
     */
    public static function getActiveUsersCount()
    {
        return Cache::remember('active_users_count', now()->addMinutes(5), function () {
            return static::activeInCurrentHour()->count();
        });
    }
}