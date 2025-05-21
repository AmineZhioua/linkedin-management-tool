<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ExtraInformation extends Model
{
    protected $table = 'user_additional_info';

    protected $fillable = [
        'user_id',
        'user_image',
        'profession',
        'adresse',
        'telephone',
        'nom_entreprise',
        'adresse_entreprise'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if the user has a valid image.
     *
     * @return bool
     */
    public function hasImage()
    {
        return !empty($this->user_image) && Storage::disk('public')->exists($this->user_image);
    }

    /**
     * Get the full URL for the user image.
     *
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        return $this->hasImage() ? Storage::disk('public')->url($this->user_image) : null;
    }
}
