<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Auth::routes(['verify' => true, 'register' => true]);

Route::prefix('/')->group(function () {

    Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

    Route::get('/pim/idiomas/cargar-idiomas', [App\Http\Controllers\IdiomasController::class, 'cargarIdiomas']);

    Route::get('/pim/productos/cargar-productos', [App\Http\Controllers\ProductosController::class, 'cargarProductos']);
    Route::delete('/pim/productos/eliminar-producto/{id_producto}', [App\Http\Controllers\ProductosController::class, 'eliminarProducto']);
    Route::post('/pim/productos/crear-producto', [App\Http\Controllers\ProductosController::class, 'crearProducto']);
    Route::put('/pim/productos/guardar-producto-editado', [App\Http\Controllers\ProductosController::class, 'guardarProductoEditado']);
    Route::get('/pim/productos/cargar-datos-producto-editar/{id_producto}', [App\Http\Controllers\ProductosController::class, 'cargarDatosProductoEditar']);
    Route::post('/pim/media/add-media-producto', [App\Http\Controllers\ProductosController::class, 'addMediaToProduct']);

    Route::get('/pim/atributos/cargar-atributos', [App\Http\Controllers\AtributosController::class, 'cargarAtributos']);
    Route::post('/pim/atributos/crear-atributo', [App\Http\Controllers\AtributosController::class, 'crearAtributo']);

    Route::get('/pim/tiendas/cargar-tiendas', [App\Http\Controllers\TiendasController::class, 'cargarTiendas']);
    Route::get('/pim/tiendas/cargar-productos-tienda/{id_tienda}', [App\Http\Controllers\TiendasController::class, 'cargarProductosTienda']);
    Route::get('/pim/tiendas/cargar-todos-productos/{id_tienda}', [App\Http\Controllers\TiendasController::class, 'cargarTodosProductos']);
    Route::post('/pim/tiendas/add-productos-tienda/{id_tienda}', [App\Http\Controllers\TiendasController::class, 'addProductosTienda']);
    Route::post('/pim/tiendas/remove-productos-tienda/{id_tienda}', [App\Http\Controllers\TiendasController::class, 'removeProductosTienda']);

    // IMPORTANTE, ESTO ES PARA ELIMINAR EL # DE LA URL
    Route::get('/{path}', [App\Http\Controllers\HomeController::class, 'index'])->where('path', '.*');
});
