<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'linkedin',
        'whatsapp',
        'date_expiration',
    ];

    protected $casts = [
        'linkedin' => 'boolean',
        'whatsapp' => 'boolean',
        'date_expiration' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
