<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosImgTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pim_productos_img', function (Blueprint $table) {
            $table->bigIncrements('id_producto_img');
            $table->unsignedBigInteger('id_producto')->nullable();
            $table->string('url_img', 450)->nullable();
            $table->boolean('img_principal')->default(false);
            
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
        Schema::dropIfExists('pim_productos_img');
    }
}
