<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtributoIdioma extends Model
{
    use HasFactory;

    protected $table = 'pim_atributos_idioma';
    protected $primaryKey = 'id_atributo_idioma';

    protected $fillable = [
        'id_atributo', 'id_idioma', 'valor_atributo'
    ];
}
