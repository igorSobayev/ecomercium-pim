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
        'ean13', 'marca', 'peso'
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
}
