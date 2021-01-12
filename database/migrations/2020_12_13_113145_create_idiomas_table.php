<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdiomasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pim_idiomas', function (Blueprint $table) {
            $table->bigIncrements('id_idioma');
            $table->string('nombre')->nullable();
            $table->string('prefijo_idioma', 5)->nullable();
            $table->string('icono_idioma')->nullable();
            $table->boolean('activo')->default(true);
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
        Schema::dropIfExists('pim_idiomas');
    }
}
