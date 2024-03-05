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
            'description' => '',
            'image' => 'conquer.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Colonizer",
            'code' => 2,
            'description' => 'Construction Cost -10% and Protection +10%',
            'image' => 'key.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Space Titan",
            'code' => 3,
            'description' => 'Robot Construction Speed +20% / Attack +2% / Research Speed -20% / Mining Speed -5%',
            'image' => 'spacetitan.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Researcher",
            'code' => 4,
            'description' => 'Research Speed +20% / Construction Speed decreases -20%',
            'image' => 'researcher.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Engineer",
            'code' => 5,
            'description' => 'Ship Construction Speed +20% / Research Speed -20%',
            'image' => 'engineer.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Protector",
            'code' => 6,
            'description' => 'Protection +20%',
            'image' => 'protector.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Builder",
            'code' => 7,
            'description' => 'Construction Cost -20% / Robot and Ship Construction Speed decreases -20%',
            'image' => 'builder.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Navigator",
            'code' => 8,
            'description' => 'Travel Speed +20% / Robot and Ship Construction Speed decreases -20%',
            'image' => 'navigator.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Miner",
            'code' => 9,
            'description' => 'Mining Speed +2% / Protection -20%',
            'image' => 'miner.png'
        ]);       
    }
}