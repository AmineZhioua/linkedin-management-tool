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
        'suspend_end',
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
            'suspend_end' => 'datetime',
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
            $searchTerm = '%' . $search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            });
        }

        return $query;
    }

    /**
     * Scope a query to order users by a specified column.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $column
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrder($query, $column = 'name', $direction = 'asc')
    {
        $validColumns = ['name', 'email', 'last_activity', 'created_at'];
        $column = in_array($column, $validColumns) ? $column : 'name';
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'asc';

        return $query->orderBy($column, $direction);
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
     * Scope a query to get non-suspended users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotSuspended($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('suspend_end')
              ->orWhere('suspend_end', '<', now());
        });
    }

    /**
     * Get cached users with optional search.
     *
     * @param string|null $search
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public static function getCachedUsers($search = null)
    {
        $cacheKey = 'users_' . md5(($search ?? 'all') . '_page_' . request()->get('page', 1));

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($search) {
            return static::filter($search)
                ->order('name', 'asc')
                ->paginate(10);
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
            return static::activeInCurrentHour()->notSuspended()->count();
        });
    }

    /**
     * Check if the user is currently suspended.
     *
     * @return bool
     */
    public function isSuspended()
    {
        return $this->suspend_end && now()->lessThan($this->suspend_end);
    }

    /**
     * Clear cached user data when user is updated or deleted.
     *
     * @return void
     */
    public static function clearUserCaches()
    {
        Cache::forget('active_users_count');
    
        // Clear all cached user pages
        $page = 1;
        while (Cache::has('users_' . md5('all_page_' . $page))) {
            Cache::forget('users_' . md5('all_page_' . $page));
            $page++;
        }
    
        // Clear cached search results
        if (request()->has('search')) {
            $search = request()->query('search');
            $page = 1;
            while (Cache::has('users_' . md5($search . '_page_' . $page))) {
                Cache::forget('users_' . md5($search . '_page_' . $page));
                $page++;
            }
        }
    }

    /**
     * Boot the model to clear caches on certain events.
     */
    protected static function booted()
    {
        static::created(function () {
            static::clearUserCaches();
        });

        static::updated(function () {
            static::clearUserCaches();
        });

        static::deleted(function () {
            static::clearUserCaches();
        });
    }
}