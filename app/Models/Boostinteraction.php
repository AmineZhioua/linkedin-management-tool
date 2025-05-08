<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boostinteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'linkedin_user_id',
        'post_id', 
        'post_url', 
        'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function linkedinUser() {
        return $this->belongsTo(LinkedinUser::class);
    }

    public function post() {
        return $this->belongsTo(ScheduledLinkedinPost::class);
    }
}