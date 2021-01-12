<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atributo extends Model
{
    use HasFactory;

    protected $table = 'pim_atributos';
    protected $primaryKey = 'id_atributo';

    protected $fillable = [
        'tipo_atributo', 'color'
    ];
}
