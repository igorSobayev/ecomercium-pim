<?php

namespace App\Http\Controllers;

use App\Models\Idioma;
use Illuminate\Http\Request;

class IdiomasController extends Controller
{
    //

    public function cargarIdiomas(Request $request)
    {

        $idiomas = Idioma::select('id_idioma', 'nombre', 'icono_idioma')->where('activo', true)->get();

        return $idiomas;
    }
}
