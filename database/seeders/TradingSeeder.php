<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TradingSeeder  extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // DB::table('users')->insert(['name' => 'Kleiton','email' => 'kleiton@com.br','password' => bcrypt('123456'),]);
        //(idPlanetCreator, idMarket, resource, `type`, quantity, price, status, createdAt, updatedAt)
        $resurce = ['Crystal','Uranium','Metal'];
        for ($i = 1; $i <= 150; $i++) {
            $quantidade = rand(1,20);
            $precoUnitario =  rand(1, 1000);
            DB::table('trading')->insert(
                [
                    'idPlanetCreator' => $i,
                    'idMarket' => 1,
                    'resource' =>$resurce[array_rand($resurce)],
                    'type' => $i % 2 ? 'P' : 'S',
                    'quantity' => $quantidade,
                    'price' => $precoUnitario,
                    'total' => ($quantidade * $precoUnitario),
                    'status' => true
                ]
            );
        }

        /*
        $faker = Faker::create();
        for ($i = 0; $i < 150; $i++) {
            $name = $faker->name;
            $email = $faker->unique()->safeEmail;
        
            DB::table('users')->insert([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt('123456'),
            ]);
        }*/
    }
}
