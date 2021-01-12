<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtributoCombinacion extends Model
{
    use HasFactory;

    protected $table = 'pim_atributos_combinaciones';
    protected $primaryKey = 'id_atributo_combinacion';
    protected $fillable = [
        'id_atributo', 'id_combinacion'
    ];
}
