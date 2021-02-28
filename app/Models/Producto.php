<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'pim_productos';
    protected $primaryKey = 'id_producto';

    protected $fillable = [
        'referencia', 'precio_sin_iva', 'precio_coste',
        'cantidad', 'producto_combinacion', 'activo',
        'ean13', 'marca', 'peso', 'cod_arancel'
    ];

    public function eliminarImgAsociadas(int $id_producto)
    {
        $imagenes_producto = ProductoImagen::select('id_producto', 'url_img', 'id_producto_img')->where('id_producto', $id_producto)->get();
        $fallo = false;
        foreach ($imagenes_producto as $img) {
            try {
                unlink(public_path() . $img->url_img);
                ProductoImagen::where('id_producto_img', $img->id_producto_img)->delete();
            } catch (\Exception $e) {
                $fallo = true;
                continue;
            }
        }
        return $fallo;
    }

    public function getStock(int $id_producto)
    {
        $stock = $this->select('cantidad')
            ->where('id_producto', $id_producto)->get()->pluck('cantidad')[0];
        $stock_combinaciones = Combinacion::select('cantidad')
            ->where('id_producto', $id_producto)->get()->sum('cantidad');
        return $stock + $stock_combinaciones;
    }

    /**
     * Recuperamos todos los datos del producto que están en el pim para subir o actualizar el producto
     * en una tienda en especifico
     */
    public function getProductFullData(int $id_producto)
    {
        $product = Producto::select(
            'referencia',
            'marca',
            'precio_sin_iva',
            'precio_coste',
            'cantidad',
            'producto_combinacion',
            'activo',
            'ean13',
            'cod_arancel',
            'peso'
        )->where('id_producto', $id_producto)->first();

        $product->idiomas = ProductoIdioma::select(
            'id_idioma',
            'nombre_producto',
            'slug',
            'descr_corta',
            'descr_larga',
            'tit_seo',
            'descr_seo'
        )->where('id_producto', $id_producto)->get();

        $product->imagenes = ProductoImagen::select('url_img')->where('id_producto', $id_producto)->get()->pluck('url_img');

        if ($product->producto_combinacion == true) {
            $product->combinaciones = Combinacion::select(
                'id_combinacion',
                'referencia',
                'ean13',
                'cod_arancel',
                'precio_sin_iva',
                'cantidad',
                'peso',
                'nombre_combinacion'
            )->where('id_producto', $id_producto)->get();

            foreach ($product->combinaciones as $key => $combinacion) {

                $combinacion->atributos = AtributoCombinacion::select(
                    'tipo_atributo',
                    'color',
                    'pim_atributos.id_atributo'
                )->join('pim_atributos', 'pim_atributos.id_atributo', '=', 'pim_atributos_combinaciones.id_atributo')
                    ->where('id_combinacion', $combinacion->id_combinacion)->get();

                foreach ($combinacion->atributos as $atributo) {
                    $atributo->idiomas = AtributoIdioma::select('id_idioma', 'valor_atributo')
                        ->where('id_atributo', $atributo->id_atributo)->get();
                }
            }
        } else {
            $product->combinaciones = [];
        }

        return $product;
    }
}
