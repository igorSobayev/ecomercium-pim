<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCombinacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pim_combinaciones', function (Blueprint $table) {
            $table->bigIncrements('id_combinacion');
            $table->unsignedBigInteger('id_producto')->nullable();
            $table->string('referencia')->nullable();
            $table->string('ean13')->nullable();
            $table->string('cod_arancel')->nullable();
            $table->double('precio_sin_iva', 10, 2)->nullable();
            $table->bigInteger('cantidad')->nullable();
            $table->string('peso', 50)->nullable();
            $table->string('nombre_combinacion', 300)->nullable();

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
        Schema::dropIfExists('pim_combinaciones');
    }
}
