<?php

namespace Database\Seeders;

use App\Models\Idioma;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class IdiomasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Idioma::insert([
            [
                'nombre' => 'Español (Spanish)',
                'prefijo_idioma' => 'es',
                'activo' => true,
                'icono_idioma' => '/media/iconos/spain-flag.png',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'nombre' => 'Inglés (English)',
                'prefijo_idioma' => 'en',
                'activo' => true,
                'icono_idioma' => '/media/iconos/england-flag.png',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'nombre' => 'Portugués (Portuguese)',
                'prefijo_idioma' => 'pt',
                'activo' => false,
                'icono_idioma' => '/media/iconos/portugal-flag.png',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'nombre' => 'Francés (French)',
                'prefijo_idioma' => 'fr',
                'activo' => false,
                'icono_idioma' => '/media/iconos/france-flag.png',
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ]);
    }
}
