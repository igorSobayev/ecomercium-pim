<?php

namespace App\Http\Controllers;

use App\Models\Atributo;
use App\Models\AtributoIdioma;
use Illuminate\Http\Request;

class AtributosController extends Controller
{
    //

    public function crearAtributo(Request $request)
    {
        // Creo el atributo principal
        $atributo = Atributo::create([
            'tipo_atributo' => $request->atributo['tipo_atributo'],
            'color' => $request->atributo['color']
        ]);

        $valor_defecto = $request->atributo['idiomas'][0]['valor_atributo'];

        // Creo atributos en diferentes idiomas
        foreach ($request->atributo['idiomas'] as $atri_idioma) {
            AtributoIdioma::create([
                'id_atributo' => $atributo->id_atributo,
                'id_idioma' => $atri_idioma['id_idioma'],
                'valor_atributo' => $atri_idioma['valor_atributo'] ?? $valor_defecto
            ]);
        }

        return response()->json([
            'id_atributo' => $atributo->id_atributo,
            'message' => 'Se ha creado el atributo con éxito'
        ], 200);

        return $request;
    }

    public function cargarAtributos()
    {
        $atributos = Atributo::select('pim_atributos.id_atributo', 'tipo_atributo', 'color', 'id_idioma', 'valor_atributo')
            ->join('pim_atributos_idioma', 'pim_atributos_idioma.id_atributo', '=', 'pim_atributos.id_atributo')
            ->where('id_idioma', 1)->where('valor_atributo', '!=', '')->get();

        return $atributos;
    }
}
