<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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


    public function user() {
        return $this->hasOne(User::class);
    }
}
