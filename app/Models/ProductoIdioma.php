<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoIdioma extends Model
{
    use HasFactory;

    protected $table = 'pim_productos_idioma';
    protected $primaryKey = 'id_producto_idioma';

    protected $fillable = [
        'id_producto', 'id_idioma', 'nombre_producto',
        'slug', 'descr_corta', 'descr_larga', 'tit_seo',
        'descr_seo'
    ];
}
