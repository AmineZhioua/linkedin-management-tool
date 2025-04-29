<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'email_verified_at',
        'role',
        'post_perm', // Added post_perm to fillable attributes
        'boost_perm', // Added boost_perm to fillable attributes
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'post_perm' => 'boolean', // Cast post_perm as boolean
            'boost_perm' => 'boolean', // Cast boost_perm as boolean
        ];
    }

    /**
     * Get the linkedin user record associated with the user.
     */
    public function linkedinUser()
    {
        return $this->hasOne(LinkedinUser::class);
    }
}