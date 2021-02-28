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

    // Atributos
    Route::get('/pim/atributos/cargar-atributos', [App\Http\Controllers\AtributosController::class, 'cargarAtributos']);
    Route::get('/pim/atributos/cargar-tipos-atributos', [App\Http\Controllers\AtributosController::class, 'cargarTiposAtributos']);
    Route::get('/pim/atributos/datos-atributo-editar/{id_atributo}', [App\Http\Controllers\AtributosController::class, 'datosAtributoEditar']);
    Route::post('/pim/atributos/crear-atributo', [App\Http\Controllers\AtributosController::class, 'crearAtributo']);
    Route::put('/pim/atributos/editar-atributo', [App\Http\Controllers\AtributosController::class, 'editarAtributo']);
    Route::post('/pim/atributos/crear-tipo-atributo', [App\Http\Controllers\AtributosController::class, 'addNuevoTipo']);
    Route::put('/pim/atributos/editar-tipo-atributo', [App\Http\Controllers\AtributosController::class, 'editarTipo']);
    Route::delete('/pim/atributos/eliminar-tipo-atributo/{id_tipo_atributo}', [App\Http\Controllers\AtributosController::class, 'eliminarTipoAtributo']);
    Route::delete('/pim/atributos/eliminar-atributo/{id_atributo}', [App\Http\Controllers\AtributosController::class, 'eliminarAtributo']);

    Route::get('/pim/tiendas/cargar-tiendas', [App\Http\Controllers\TiendasController::class, 'cargarTiendas']);
    Route::get('/pim/tiendas/cargar-productos-tienda/{id_tienda}', [App\Http\Controllers\TiendasController::class, 'cargarProductosTienda']);
    Route::get('/pim/tiendas/cargar-todos-productos/{id_tienda}', [App\Http\Controllers\TiendasController::class, 'cargarTodosProductos']);
    Route::post('/pim/tiendas/add-productos-tienda/{id_tienda}', [App\Http\Controllers\TiendasController::class, 'addProductosTienda']);
    Route::post('/pim/tiendas/remove-productos-tienda/{id_tienda}', [App\Http\Controllers\TiendasController::class, 'removeProductosTienda']);

    // Rutas para controlar las conexiones de los webservices
    Route::get('/pim/ps-webservices/test', [App\Http\Controllers\PrestaShopWebservicesController::class, 'test']);
    Route::get('/pim/ps-webservices/test2', [App\Http\Controllers\PrestaShopWebservicesController::class, 'test2']);
    Route::get('/pim/ps-webservices/test3', [App\Http\Controllers\PrestaShopWebservicesController::class, 'test3']);
    Route::get('/pim/ps-webservices/test4', [App\Http\Controllers\PrestaShopWebservicesController::class, 'test4']);
    Route::get('/pim/ps-webservices/test5', [App\Http\Controllers\PrestaShopWebservicesController::class, 'test5']);
    Route::get('/pim/ps-webservices/test6', [App\Http\Controllers\PrestaShopWebservicesController::class, 'test6']);

    // IMPORTANTE, ESTO ES PARA ELIMINAR EL # DE LA URL
    Route::get('/{path}', [App\Http\Controllers\HomeController::class, 'index'])->where('path', '.*');
});
