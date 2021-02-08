<?php

namespace App\Http\Controllers;

use App\Models\Atributo;
use App\Models\AtributoIdioma;
use App\Models\TipoAtributo;
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

    public function editarAtributo(Request $request)
    {

        Atributo::where('id_atributo', $request->atributo['id_atributo'])
            ->update([
                'tipo_atributo' => $request->atributo['tipo_atributo'],
                'color' => $request->atributo['color']
            ]);

        foreach ($request->atributo['idiomas'] as $atri_idioma) {
            AtributoIdioma::where('id_atributo_idioma', $atri_idioma['id_atributo_idioma'])
                ->update([
                    'valor_atributo' => $atri_idioma['valor_atributo']
                ]);
        }

        return response()->json([
            'message' => 'Se ha editado el atributo con éxito'
        ], 200);
    }

    public function cargarAtributos()
    {
        $atributos = Atributo::select('pim_atributos.id_atributo', 'tipo_atributo', 'color', 'id_idioma', 'valor_atributo')
            ->join('pim_atributos_idioma', 'pim_atributos_idioma.id_atributo', '=', 'pim_atributos.id_atributo')
            ->where('id_idioma', 1)->where('valor_atributo', '!=', '')->get();

        return $atributos;
    }

    public function cargarTiposAtributos(Request $request)
    {
        $tipos_atributos = TipoAtributo::select('id_tipo_atributo', 'tipo_atributo')->get();

        return $tipos_atributos;
        return response()->json([
            'message' => 'Se han cargado los tipos de atributo con exito',
            'tipos_atributos' => $tipos_atributos
        ], 200);
    }

    public function addNuevoTipo(Request $request)
    {
        TipoAtributo::create([
            'tipo_atributo' => $request->tipo_atributo_nuevo['tipo_atributo']
        ]);

        return response()->json([
            'message' => 'Se ha creado el nuevo tipo con éxito'
        ], 200);
    }

    public function editarTipo(Request $request)
    {
        $old_tipo = TipoAtributo::where('id_tipo_atributo', $request->tipo_atributo_editado['id_tipo_atributo'])->first()->tipo_atributo;
        TipoAtributo::where('id_tipo_atributo', $request->tipo_atributo_editado['id_tipo_atributo'])
            ->update([
                'tipo_atributo' => $request->tipo_atributo_editado['tipo_atributo']
            ]);

        Atributo::where('tipo_atributo', $old_tipo)
            ->update([
                'tipo_atributo' => $request->tipo_atributo_editado['tipo_atributo']
            ]);

        return response()->json([
            'message' => 'Se ha editado el tipo con éxito'
        ], 200);
    }

    public function eliminarTipoAtributo(int $id_tipo_atributo)
    {
        // Obtenemos el nombre dle tipo
        $tipo_atributo = TipoAtributo::where('id_tipo_atributo', $id_tipo_atributo)->first()->tipo_atributo;
        // Eliminamos el tipo
        TipoAtributo::where('id_tipo_atributo', $id_tipo_atributo)->delete();
        // Eliminamos los atributos asociados
        Atributo::where('tipo_atributo', $tipo_atributo)->delete();

        return response()->json([
            'message' => 'Se ha eliminado el tipo de atributo'
        ], 200);
    }

    public function eliminarAtributo(int $id_atributo)
    {
        Atributo::where('id_atributo', $id_atributo)->delete();

        return response()->json([
            'message' => 'Se ha eliminado el atributo con éxito'
        ], 200);
    }

    public function datosAtributoEditar(int $id_atributo)
    {
        $atributo = Atributo::where('id_atributo', $id_atributo)->first();
        $idiomas = AtributoIdioma::where('id_atributo', $id_atributo)->get();
        $atributo->idiomas = $idiomas;

        return response()->json([
            'message' => 'Se han cargado los datos con éxito',
            'atributo' => $atributo
        ], 200);
    }
}
