<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtributosCombinacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pim_atributos_combinaciones', function (Blueprint $table) {
            $table->bigIncrements('id_atributo_combinacion');
            $table->unsignedBigInteger('id_atributo')->nullable();
            $table->unsignedBigInteger('id_combinacion')->nullable();

            $table->foreign('id_atributo')->references('id_atributo')->on('pim_atributos')->onDelete('cascade');
            $table->foreign('id_combinacion')->references('id_combinacion')->on('pim_combinaciones')->onDelete('cascade');
            
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
        Schema::dropIfExists('pim_atributos_combinaciones');
    }
}
