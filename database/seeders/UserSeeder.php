<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@com.br',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert(['name' => 'Kleiton','email' => 'kleiton@com.br','password' => bcrypt('123456'),]);

        DB::table('users')->insert([
            'name' => 'Andry',
            'email' => 'andry@com.br',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'name' => 'Laris',
            'email' => 'laris@com.br',
            'password' => bcrypt('123456'),
        ]);

        $faker = Faker::create();
        for ($i = 0; $i < 150; $i++) {
            $name = $faker->name;
            $email = $faker->unique()->safeEmail;
        
            DB::table('users')->insert([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt('123456'),
            ]);
        }

    }
}
