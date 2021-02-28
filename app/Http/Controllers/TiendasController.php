<?php

namespace App\Http\Controllers;

use App\Models\PrestaConnector;
use App\Models\Producto;
use App\Models\Tienda;
use App\Models\TiendaProducto;
use Illuminate\Http\Request;

class TiendasController extends Controller
{
    //

    public function cargarTiendas()
    {
        $obj_tienda = new Tienda();
        $tiendas = Tienda::select('nombre_tienda', 'id_tienda')
            ->get();

        foreach ($tiendas as $tienda) {
            $tienda->num_productos = $obj_tienda->getNumProductos($tienda->id_tienda);
            $tienda->stock_total = $obj_tienda->getStockTotal($tienda->id_tienda);
        }
        return $tiendas;
    }

    public function cargarProductosTienda(int $id_tienda = null)
    {
        return Tienda::select(
            'nombre_tienda',
            'pim_tiendas.id_tienda',
            'pim_productos.id_producto',
            'pim_productos.referencia',
            'pim_productos.activo',
            'pim_productos_idioma.nombre_producto',
            'pim_tiendas_productos.id_tienda_producto'
        )
            ->join('pim_tiendas_productos', 'pim_tiendas_productos.id_tienda', '=', 'pim_tiendas.id_tienda')
            ->join('pim_productos', 'pim_productos.id_producto', '=', 'pim_tiendas_productos.id_producto')
            ->join('pim_productos_idioma', 'pim_productos_idioma.id_producto', '=', 'pim_productos.id_producto')
            ->where('pim_productos_idioma.id_idioma', 1)
            ->where('pim_tiendas.id_tienda', $id_tienda)->get();
    }

    public function cargarTodosProductos(int $id_tienda = null)
    {
        $tienda = new TiendaProducto();
        $productos_en_tienda = $tienda->productosEnTienda($id_tienda);
        // return $productos_en_tienda;
        $productos = Producto::select(
            'pim_productos.id_producto',
            'pim_productos.referencia',
            'pim_productos.activo',
            'pim_productos_idioma.nombre_producto',
            'pim_tiendas_productos.id_tienda_producto'
        )
            ->join('pim_productos_idioma', 'pim_productos_idioma.id_producto', '=', 'pim_productos.id_producto')
            ->leftJoin('pim_tiendas_productos', 'pim_tiendas_productos.id_producto', '=', 'pim_productos.id_producto')
            ->where('pim_productos_idioma.id_idioma', 1)
            ->where(function ($query) use ($id_tienda, $productos_en_tienda) {
                $query->whereNull('pim_tiendas_productos.id_tienda_producto');
                // $query->orWhere('pim_tiendas_productos.id_tienda', '!=', $id_tienda);
                $query->orWhereNotIn('pim_tiendas_productos.id_producto', $productos_en_tienda);
            })
            ->get();

        return $productos;
    }

    public function addProductosTienda(int $id_tienda = null, Request $request)
    {
        $obj_ps = new PrestaConnector();
        
        if (!is_null($id_tienda)) {
            foreach ($request->productos as $producto) {
                TiendaProducto::create([
                    'id_tienda' => $id_tienda,
                    'id_producto' => $producto
                ]);

                // Se añade el producto a la tienda
                $obj_ps->addProductToTienda($id_tienda, $producto);
            }

            return response()->json([
                'message' => 'Se han añadido los productos con éxito'
            ], 200);
        } else {
            return response()->json([
                'message' => 'No se ha encontrado la tienda'
            ], 500);
        }
    }

    public function removeProductosTienda(int $id_tienda = null, Request $request)
    {
        if (!is_null($id_tienda)) {
            foreach ($request->productos as $prod) {
                TiendaProducto::where('id_tienda_producto', $prod)->delete();
            }
            return response()->json([
                'message' => 'Se han eliminado los productos con éxito'
            ], 200);
        } else {
            return response()->json([
                'message' => 'No se ha encontrado la tienda'
            ], 500);
        }
    }
}
