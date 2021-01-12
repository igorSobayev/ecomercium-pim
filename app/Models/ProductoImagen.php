<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoImagen extends Model
{
    use HasFactory;

    protected $table = 'pim_productos_img';
    protected $primaryKey = 'id_producto_img';

    protected $fillable = [
        'id_producto', 'url_img', 'img_principal'
    ];

    public function eliminarMedia(int $id_producto_img)
    {
        $media_eliminar = $this->select('id_producto', 'url_img', 'id_producto_img')
            ->where('id_producto_img', $id_producto_img)->first();

        $fallo = false;

        try {
            unlink(public_path() . $media_eliminar->url_img);
            ProductoImagen::where('id_producto_img', $media_eliminar->id_producto_img)->delete();
        } catch (\Exception $e) {
            $fallo = true;
        }

        return $fallo;
    }
}
