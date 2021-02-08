<?php

namespace Database\Seeders;

use App\Models\TipoAtributo;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AtributosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        TipoAtributo::insert(
            [
                [
                    'tipo_atributo' => 'talla',
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString()
                ],
                [
                    'tipo_atributo' => 'color',
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString()
                ]
            ]
        );
    }
}
