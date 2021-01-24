<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTiendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pim_tiendas', function (Blueprint $table) {
            $table->bigIncrements('id_tienda');
            $table->string('nombre_tienda', 250)->nulalble();
            $table->string('tipo_tienda', 100)->nulalble();
            $table->string('api_key', 350)->nulalble();
            $table->string('store_root', 150)->nulalble();
            $table->boolean('debug')->default(false);
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
        Schema::dropIfExists('pim_tiendas');
    }
}
