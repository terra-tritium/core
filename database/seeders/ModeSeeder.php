<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modes')->insert([
            'name' => "Conquer",
            'code' => 1,
            'description' => 'description',
            'image' => 'mode01.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Space Titan",
            'code' => 2,
            'description' => 'description',
            'image' => 'mode01.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Researcher",
            'code' => 3,
            'description' => 'description',
            'image' => 'mode01.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Engineer",
            'code' => 4,
            'description' => 'description',
            'image' => 'mode01.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Protector",
            'code' => 5,
            'description' => 'description',
            'image' => 'mode01.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Builder",
            'code' => 6,
            'description' => 'description',
            'image' => 'mode01.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Navigator",
            'code' => 7,
            'description' => 'description',
            'image' => 'mode01.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Miner",
            'code' => 8,
            'description' => 'description',
            'image' => 'mode01.png'
        ]);
    }
}
