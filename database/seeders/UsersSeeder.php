<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $user = new User();
        $user->name = 'Igor Desarrollo';
        $user->email = 'isobayev@gmail.com';
        $user->password = bcrypt('ginettag50');
        $user->save();

        $user = new User();
        $user->name = 'Test Ecomercium';
        $user->email = 'test@ecomercium.com';
        $user->password = bcrypt('ecomercium_PIM_2021');
        $user->save();
    }
}
