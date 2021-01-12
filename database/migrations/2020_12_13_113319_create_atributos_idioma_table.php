<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtributosIdiomaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pim_atributos_idioma', function (Blueprint $table) {
            $table->bigIncrements('id_atributo_idioma');
            $table->unsignedBigInteger('id_atributo')->nullable();
            $table->unsignedBigInteger('id_idioma')->nullable();
            $table->string('valor_atributo', 350)->nullable();

            $table->foreign('id_atributo')->references('id_atributo')->on('pim_atributos')->onDelete('cascade');
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
        Schema::dropIfExists('pim_atributos_idioma');
    }
}
