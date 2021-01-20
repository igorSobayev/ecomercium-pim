<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pim_productos', function (Blueprint $table) {
            $table->bigIncrements('id_producto');
            $table->string('referencia')->nullable();
            $table->string('marca', 160)->nullable();
            $table->double('precio_sin_iva', 10, 2)->nullable();
            $table->double('precio_coste', 10, 2)->nullable();
            $table->bigInteger('cantidad')->nullable();
            $table->boolean('producto_combinacion')->default(false);
            $table->boolean('activo')->default(true);
            $table->string('ean13')->nullable();
            $table->string('cod_arancel')->nullable();
            $table->string('peso', 50)->nullable();
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
        Schema::dropIfExists('pim_productos');
    }
}
