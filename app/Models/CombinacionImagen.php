<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CombinacionImagen extends Model
{
    use HasFactory;

    protected $table = 'pim_combinaciones_img';
    protected $primaryKey = 'id_combinacion_img';

    protected $fillable = [
        'id_combinacion', 'url_img', 'img_principal'
    ];
}
