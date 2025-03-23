<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlateformeMarque extends Model
{
    use HasFactory;

    protected $table = 'plateforme_marque';

    protected $fillable = [
        'user_id',
        'nom_marque',
        'domaine_marque',
        'logo_marque',
        'description_marque',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
