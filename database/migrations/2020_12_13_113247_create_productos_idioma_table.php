<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosIdiomaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pim_productos_idioma', function (Blueprint $table) {
            $table->bigIncrements('id_producto_idioma');
            $table->unsignedBigInteger('id_producto')->nullable();
            $table->unsignedBigInteger('id_idioma')->nullable();
            $table->string('nombre_producto', 300)->nullable();
            $table->string('slug', 300)->nullable();
            $table->mediumText('descr_corta')->nullable();
            $table->mediumText('descr_larga')->nullable();
            $table->string('tit_seo', 300)->nullable();
            $table->string('descr_seo', 500)->nullable();

            $table->foreign('id_producto')->references('id_producto')->on('pim_productos')->onDelete('cascade');
            $table->foreign('id_idioma')->references('id_idioma')->on('pim_idiomas')->onDelete('set null');

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
        Schema::dropIfExists('pim_productos_idioma');
    }
}
