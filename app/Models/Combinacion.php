<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Combinacion extends Model
{
    use HasFactory;

    protected $table = 'pim_combinaciones';
    protected $primaryKey = 'id_combinacion';

    protected $fillable = [
        'id_producto', 'referencia', 'ean13', 'precio_sin_iva',
        'cantidad', 'nombre_combinacion'
    ];
}
