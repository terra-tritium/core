<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StrategySeeder extends Seeder
{
    /** 
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('strategies')->insert([
            'name' => "Line",
            'code' => 1,
            'description' => 'description',
            'image' => 'strategy01.png',
            'bonus' => 'Droides soma +2A',
            'attack' => 25,
            'defense' => 0
        ]);
        DB::table('strategies')->insert([
            'name' => "Sniper",
            'code' => 2,
            'description' => 'description',
            'image' => 'strategy02.png',
            'bonus' => 'Droides soma +2A',
            'attack' => 23,
            'defense' => 2
        ]);

        DB::table('strategies')->insert([
            'name' => "Guerrilla",
            'code' => 3,
            'description' => 'description',
            'image' => 'strategy03.png',
            'bonus' => 'Droides soma +2A',
            'attack' => 22,
            'defense' => 3
        ]);
        DB::table('strategies')->insert([
            'name' => "Diamond",
            'code' => 4,
            'description' => 'description',
            'image' => 'strategy04.png',
            'bonus' => 'Droides soma +2D',
            'attack' => 20,
            'defense' => 5
        ]);
        DB::table('strategies')->insert([
            'name' => "Wedge",
            'code' => 5,
            'description' => 'description',
            'image' => 'strategy05.png',
            'bonus' => 'Droides soma +2D',
            'attack' => 18,
            'defense' => 7
        ]);

        DB::table('strategies')->insert([
            'name' => "Star",
            'code' => 6,
            'description' => 'description',
            'image' => 'strategy06.png',
            'bonus' => 'Droides soma +2D',
            'attack' => 16,
            'defense' => 9
        ]);
        DB::table('strategies')->insert([
            'name' => "Delta",
            'code' => 7,
            'description' => 'description',
            'image' => 'strategy07.png',
            'bonus' => 'Veículos de Apoio soma +1A e +1D',
            'attack' => 14,
            'defense' => 11
        ]);
        DB::table('strategies')->insert([
            'name' => "Diagonal",
            'code' => 8,
            'description' => 'description',
            'image' => 'strategy08.png',
            'bonus' => 'Veículos de Apoio soma +1A e +1D',
            'attack' => 12,
            'defense' => 13
        ]);
        DB::table('strategies')->insert([
            'name' => "Column",
            'code' => 9,
            'description' => 'description',
            'image' => 'strategy09.png',
            'bonus' => 'Lançador soma +2D',
            'attack' => 10,
            'defense' => 15
        ]);

        DB::table('strategies')->insert([
            'name' => "Dual Column",
            'code' => 10,
            'description' => 'description',
            'image' => 'strategy10.png',
            'bonus' => 'Lançador soma +2D',
            'attack' => 8,
            'defense' => 17
        ]);
        DB::table('strategies')->insert([
            'name' => "Flanks",
            'code' => 11,
            'description' => 'description',
            'image' => 'strategy11.png',
            'bonus' => 'Lançador soma +2D',
            'attack' => 5,
            'defense' => 20
        ]);

        

       

        

      

       

      
    }
}
