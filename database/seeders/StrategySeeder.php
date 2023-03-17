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
            'name' => "Wedge",
            'code' => 1,
            'description' => 'description',
            'image' => 'strategy01.png'
        ]);

        DB::table('strategies')->insert([
            'name' => "Delta",
            'code' => 2,
            'description' => 'description',
            'image' => 'strategy02.png'
        ]);

        DB::table('strategies')->insert([
            'name' => "Line",
            'code' => 3,
            'description' => 'description',
            'image' => 'strategy03.png'
        ]);

        DB::table('strategies')->insert([
            'name' => "Sniper",
            'code' => 4,
            'description' => 'description',
            'image' => 'strategy04.png'
        ]);

        DB::table('strategies')->insert([
            'name' => "Column",
            'code' => 5,
            'description' => 'description',
            'image' => 'strategy05.png'
        ]);

        DB::table('strategies')->insert([
            'name' => "Diamond",
            'code' => 6,
            'description' => 'description',
            'image' => 'strategy06.png'
        ]);

        DB::table('strategies')->insert([
            'name' => "Star",
            'code' => 7,
            'description' => 'description',
            'image' => 'strategy07.png'
        ]);

        DB::table('strategies')->insert([
            'name' => "Diagonal",
            'code' => 8,
            'description' => 'description',
            'image' => 'strategy08.png'
        ]);

        DB::table('strategies')->insert([
            'name' => "Dual Column",
            'code' => 9,
            'description' => 'description',
            'image' => 'strategy09.png'
        ]);

        DB::table('strategies')->insert([
            'name' => "Flanks",
            'code' => 10,
            'description' => 'description',
            'image' => 'strategy10.png'
        ]);
    }
}
