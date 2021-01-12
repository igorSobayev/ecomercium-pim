<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCombinacionesImgTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pim_combinaciones_img', function (Blueprint $table) {
            $table->bigIncrements('id_combinacion_img');
            $table->unsignedBigInteger('id_combinacion')->nullable();
            $table->string('url_img', 450)->nullable();
            $table->boolean('img_principal')->default(false);

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
        Schema::dropIfExists('pim_combinaciones_img');
    }
}
