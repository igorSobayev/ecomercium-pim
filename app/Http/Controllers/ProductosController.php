<?php

namespace App\Http\Controllers;

use App\Models\AtributoCombinacion;
use App\Models\Combinacion;
use App\Models\Producto;
use App\Models\ProductoIdioma;
use App\Models\ProductoImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Image;

class ProductosController extends Controller
{
    //

    public function cargarProductos()
    {
        return Producto::select('pim_productos.id_producto', 'referencia', 'precio_sin_iva', 'cantidad', 'nombre_producto')
            ->join('pim_productos_idioma', 'pim_productos_idioma.id_producto', '=', 'pim_productos.id_producto')
            ->where('pim_productos_idioma.id_idioma', 1)->get();
    }

    public function crearProducto(Request $request)
    {
        // Creo el producto principal
        $producto = Producto::create([
            'referencia' => $request->producto['referencia'],
            'activo' => $request->producto['activo'],
            'precio_sin_iva' => $request->producto['precio_sin_iva'],
            'precio_coste' => $request->producto['precio_coste'],
            'cantidad' => $request->producto['cantidad'],
            'producto_combinacion' => $request->producto['producto_combinacion'],
            'ean13' => $request->producto['ean13'],
            'marca' => $request->producto['marca']
        ]);

        // Valores por defecto en caso de estar vacío alguno de los otros idiomas
        $nombre_producto_defecto = $request->producto['idiomas'][0]['nombre_producto'];

        if ($request->producto['idiomas'][0]['slug'] == '') {
            $slug_defecto = Str::slug($nombre_producto_defecto);
        } else {
            $slug_defecto = Str::slug($request->producto['idiomas'][0]['slug']);
        }

        $descr_corta_defecto = $request->producto['idiomas'][0]['descr_corta'];
        $descr_larga_defecto = $request->producto['idiomas'][0]['descr_larga'];

        if ($request->producto['idiomas'][0]['tit_seo'] == '') {
            $tit_seo_defecto = $nombre_producto_defecto;
        } else {
            $tit_seo_defecto = $request->producto['idiomas'][0]['tit_seo'];
        }

        $descr_seo_defecto = $request->producto['idiomas'][0]['descr_seo'];

        // Creo productos en diferentes idiomas
        foreach ($request->producto['idiomas'] as $pro_idioma) {
            if ($pro_idioma['slug'] == '') {
                $slug_final = $slug_defecto;
            } else {
                $slug_final = Str::slug($pro_idioma['slug']);
            }
            ProductoIdioma::create([
                'id_producto' => $producto->id_producto,
                'id_idioma' => $pro_idioma['id_idioma'],
                'nombre_producto' => $pro_idioma['nombre_producto'] ?? $nombre_producto_defecto,
                'slug' => $slug_final,
                'descr_corta' => $pro_idioma['descr_corta'] ?? $descr_corta_defecto,
                'descr_larga' => $pro_idioma['descr_larga'] ?? $descr_larga_defecto,
                'tit_seo' => $pro_idioma['tit_seo'] ?? $tit_seo_defecto,
                'descr_seo' => $pro_idioma['descr_seo'] ?? $descr_seo_defecto
            ]);
        }

        if ($request->producto['producto_combinacion']) {
            foreach ($request->combinaciones as $key => $combinacion) {
                $id_combinacion = Combinacion::create([
                    'id_producto' => $producto->id_producto,
                    'referencia' => $combinacion['referencia'] ?? $request->producto['referencia'] . '-' . $key,
                    'ean13' => $combinacion['ean13'] ?? $request->producto['ean13'] . '-' . $key,
                    'precio_sin_iva' => $combinacion['precio'],
                    'cantidad' => $combinacion['stock'],
                    'nombre_combinacion' => $combinacion['nombre_combinacion']
                ]);

                foreach ($combinacion['id_atributo'] as $atri_combi) {
                    AtributoCombinacion::create([
                        'id_atributo' => $atri_combi,
                        'id_combinacion' => $id_combinacion->id_combinacion
                    ]);
                }
            }
        }

        return response()->json([
            'id_producto' => $producto->id_producto,
            'message' => 'Se ha creado el producto con éxito'
        ], 200);

        return $request;
    }

    public function guardarProductoEditado(Request $request)
    {
        // Creo el producto principal
        $producto = Producto::where('id_producto', $request->producto['id_producto'])
            ->update([
                'referencia' => $request->producto['referencia'],
                'activo' => $request->producto['activo'],
                'precio_sin_iva' => $request->producto['precio_sin_iva'],
                'precio_coste' => $request->producto['precio_coste'],
                'cantidad' => $request->producto['cantidad'],
                'producto_combinacion' => $request->producto['producto_combinacion'],
                'ean13' => $request->producto['ean13'],
                'marca' => $request->producto['marca']
            ]);

        // Valores por defecto en caso de estar vacío alguno de los otros idiomas
        $nombre_producto_defecto = $request->producto['idiomas'][0]['nombre_producto'];

        if ($request->producto['idiomas'][0]['slug'] == '') {
            $slug_defecto = Str::slug($nombre_producto_defecto);
        } else {
            $slug_defecto = Str::slug($request->producto['idiomas'][0]['slug']);
        }

        $descr_corta_defecto = $request->producto['idiomas'][0]['descr_corta'];
        $descr_larga_defecto = $request->producto['idiomas'][0]['descr_larga'];

        if ($request->producto['idiomas'][0]['tit_seo'] == '') {
            $tit_seo_defecto = $nombre_producto_defecto;
        } else {
            $tit_seo_defecto = $request->producto['idiomas'][0]['tit_seo'];
        }

        $descr_seo_defecto = $request->producto['idiomas'][0]['descr_seo'];

        // Creo productos en diferentes idiomas
        foreach ($request->producto['idiomas'] as $pro_idioma) {
            if ($pro_idioma['slug'] == '') {
                $slug_final = $slug_defecto;
            } else {
                $slug_final = Str::slug($pro_idioma['slug']);
            }
            ProductoIdioma::where('id_producto_idioma', $pro_idioma['id_producto_idioma'])
                ->update([
                    'nombre_producto' => $pro_idioma['nombre_producto'] ?? $nombre_producto_defecto,
                    'slug' => $slug_final,
                    'descr_corta' => $pro_idioma['descr_corta'] ?? $descr_corta_defecto,
                    'descr_larga' => $pro_idioma['descr_larga'] ?? $descr_larga_defecto,
                    'tit_seo' => $pro_idioma['tit_seo'] ?? $tit_seo_defecto,
                    'descr_seo' => $pro_idioma['descr_seo'] ?? $descr_seo_defecto
                ]);
        }

        // Gestiono combinaciones
        if ($request->producto['producto_combinacion']) {
            // Creo nuevas combinaciones
            foreach ($request->combinaciones_nuevas as $key => $combinacion) {
                $id_combinacion = Combinacion::create([
                    'id_producto' => $request->producto['id_producto'],
                    'referencia' => $combinacion['referencia'] ?? $request->producto['referencia'] . '-' . $key,
                    'ean13' => $combinacion['ean13'] ?? $request->producto['ean13'] . '-' . $key,
                    'precio_sin_iva' => $combinacion['precio'],
                    'cantidad' => $combinacion['stock'],
                    'nombre_combinacion' => $combinacion['nombre_combinacion']
                ]);

                foreach ($combinacion['id_atributo'] as $atri_combi) {
                    AtributoCombinacion::create([
                        'id_atributo' => $atri_combi,
                        'id_combinacion' => $id_combinacion->id_combinacion
                    ]);
                }
            }

            // Edito las combinaciones existentes
            foreach ($request->combinaciones_actualizadas as $key => $combinacion) {
                $id_combinacion = Combinacion::where('id_combinacion', $combinacion['id_combinacion'])
                    ->update([
                        'referencia' => $combinacion['referencia'] ?? $request->producto['referencia'] . '-' . $key,
                        'ean13' => $combinacion['ean13'] ?? $request->producto['ean13'] . '-' . $key,
                        'precio_sin_iva' => $combinacion['precio'],
                        'cantidad' => $combinacion['stock'],
                        'nombre_combinacion' => $combinacion['nombre_combinacion']
                    ]);
            }

            // Elimino las combinaciones seleccionadas
            foreach ($request->combinaciones_eliminadas as $combinacion) {
                Combinacion::where('id_combinacion', $combinacion)->delete();
            }
        }

        // Gestiono las imagenes eliminadas
        $obj_media = new ProductoImagen();
        foreach ($request->media_eliminada as $media) {
            $obj_media->eliminarMedia($media);
        }

        return response()->json([
            'id_producto' => $request->producto['id_producto'],
            'message' => 'Se ha actualizado el producto con éxito'
        ], 200);

        return $request;
    }

    public function cargarDatosProductoEditar(int $id_producto)
    {
        $producto = Producto::select(
            'id_producto',
            'referencia',
            'precio_sin_iva',
            'precio_coste',
            'cantidad',
            'producto_combinacion',
            'activo',
            'ean13',
            'marca'
        )->where('id_producto', $id_producto)->first();

        $producto_idiomas = ProductoIdioma::select(
            'id_producto_idioma',
            'id_producto',
            'id_idioma',
            'nombre_producto',
            'slug',
            'descr_corta',
            'descr_larga',
            'tit_seo',
            'descr_seo'
        )->where('id_producto', $id_producto)->get();

        $producto_imagenes = ProductoImagen::select(
            'id_producto_img',
            'id_producto',
            'url_img',
            'img_principal'
        )->where('id_producto', $id_producto)->get();

        $producto_combinaciones = Combinacion::select(
            'id_combinacion',
            'id_producto',
            'referencia',
            'ean13',
            'precio_sin_iva as precio',
            'cantidad as stock',
            'nombre_combinacion'
        )->where('id_producto', $id_producto)->get();

        $producto->productos_idiomas = $producto_idiomas;
        $producto->imagenes = $producto_imagenes;
        $producto->combinaciones = $producto_combinaciones;

        return response()->json([
            'message' => 'Se ha encontrado el producto con éxito',
            'producto' => $producto
        ], 200);
    }

    public function eliminarProducto(int $id_producto)
    {
        $obj_producto = new Producto();

        $result = $obj_producto->eliminarImgAsociadas($id_producto);

        if ($result === false) {
            Producto::where('id_producto', $id_producto)->delete();
        } else {
            return response()->json([
                'message' => 'Hubo un problema al eliminar el producto'
            ], 500);
        }

        return response()->json([
            'message' => 'Se ha eliminado el producto con éxito'
        ], 200);
    }

    /**
     * Función para añadir un varios archivo a la base de datos
     * 
     * @access public
     * @param Request con todos los datos para crear el archivo (un formData) y los archivos
     * @return mensaje de éxito
     */
    public function addMediaToProduct(Request $request)
    {

        if ($request->file != null) {

            foreach ($request->file as $key => $file) {

                // Le asigno un nombre que será único al concatenar el nombre del archivo con el tiempo
                // $originalFilename = explode('.', $file->getClientOriginalName());

                $originalFilename = [
                    basename($file->getClientOriginalName(), '.' . $file->getClientOriginalExtension()),
                    $file->getClientOriginalExtension()
                ];

                // Una vez que se que tipo de archivo es lo mando a la carpeta correspondiente y guardo el enlace para utilizarlo en el front
                // IMAGEN ORIGINAL
                $img_original = Image::make($file);
                // Si la imagen es muy grande la escalo a 1920 de width y el height será auto
                if ($img_original->width() > 1920) {
                    $img_original->resize(1920, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }

                // Construyo el nombre de las imagenes, que ahora será 'toyota-xd-1920x1080-tiempo.extension'
                $filename_l = Str::slug($originalFilename[0] . '-' . $img_original->width() . 'x' . $img_original->height() . '-' . time()) . '.' . $originalFilename[1];

                // Añado el slug al media

                $path = public_path('media/media-productos/' . $filename_l);

                $img_original->save($path);

                // Guardo el objeto en la BD
                ProductoImagen::create([
                    'id_producto' => $request->id_producto[$key],
                    'url_img' => '/media/media-productos/' . $filename_l,
                    'img_principal' => true
                ]);
            }
        } else {
            return response()->json([
                'message' => 'No hay ningún archivo'
            ], 500);
        }

        return response()->json([
            'message' => 'Archivo añadido con éxito'
        ], 200);
    }
}
