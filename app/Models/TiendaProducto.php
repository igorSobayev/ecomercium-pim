<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiendaProducto extends Model
{
    use HasFactory;

    protected $table = 'pim_tiendas_productos';
    protected $primaryKey = 'id_tienda_producto';

    protected $fillable = [
        'id_tienda', 'id_producto'
    ];

    public function productosEnTienda(int $id_tienda)
    {
        return $this->select('id_producto')->where('id_tienda', $id_tienda)
            ->get()
            ->pluck('id_producto');
    }
}
