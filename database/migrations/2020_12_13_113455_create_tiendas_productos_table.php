<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTiendasProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pim_tiendas_productos', function (Blueprint $table) {
            $table->bigIncrements('id_tienda_producto');
            $table->unsignedBigInteger('id_tienda')->nullable();
            $table->unsignedBigInteger('id_producto')->nullable();
            
            $table->foreign('id_tienda')->references('id_tienda')->on('pim_tiendas')->onDelete('cascade');
            $table->foreign('id_producto')->references('id_producto')->on('pim_productos')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pim_tiendas_productos');
    }
}
