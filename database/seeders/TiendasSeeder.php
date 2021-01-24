<?php

namespace Database\Seeders;

use App\Models\Tienda;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TiendasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        Tienda::insert([
            [
                'nombre_tienda' => 'Prestashop 1',
                'tipo_tienda' => 'Prestashop',
                'api_key' => 'ZN1WB8KNU7J19U5DGV9S1S3HWK9DTT3N',
                'store_root' => 'https://pim.isobayev.com/ps-1/',
                'debug' => false,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'nombre_tienda' => 'Prestashop 2',
                'tipo_tienda' => 'Prestashop',
                'api_key' => 'TCSERTNFMC2JF5Y5BT6ZJUIL5DQK3TA4',
                'store_root' => 'https://pim.isobayev.com/ps-2/',
                'debug' => false,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'nombre_tienda' => 'Amazon',
                'tipo_tienda' => 'Amazon',
                'api_key' => '',
                'store_root' => '',
                'debug' => false,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]
        ]);
    }
}
