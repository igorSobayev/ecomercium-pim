<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoAtributo extends Model
{
    use HasFactory;

    protected $table = 'pim_tipos_atributos';
    protected $primaryKey = 'id_tipo_atributo';

    protected $fillable = [
        'tipo_atributo'
    ];
}
