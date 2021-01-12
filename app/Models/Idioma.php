<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Idioma extends Model
{
    use HasFactory;

    protected $table = 'pim_idiomas';
    protected $primaryKey = 'id_idioma';

    protected $fillable = [
        'nombre', 'prefijo_idioma', 'icono_idioma', 'activo'
    ];
}
