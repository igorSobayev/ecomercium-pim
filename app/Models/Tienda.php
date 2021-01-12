<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tienda extends Model
{
    use HasFactory;

    protected $table = 'pim_tiendas';
    protected $primaryKey = 'id_tienda';

    protected $fillable = [
        'nombre_tienda'
    ];

    public function getNumProductos(int $id_tienda)
    {
        return $this->join('pim_tiendas_productos', 'pim_tiendas_productos.id_tienda', '=', 'pim_tiendas.id_tienda')
            ->where('pim_tiendas.id_tienda', $id_tienda)->count();
    }

    public function getStockTotal(int $id_tienda)
    {
        $obj_producto = new Producto();
        $obj_tiendas_productos = new TiendaProducto();
        $productos_tienda = $obj_tiendas_productos->productosEnTienda($id_tienda);
        $stock_total = 0;
        foreach ($productos_tienda as $prod) {
            // return $obj_producto->getStock($prod);
            $stock_total += $obj_producto->getStock($prod);
        }
        return $stock_total;
    }
}
