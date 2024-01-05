<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitShipyardSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ships')->insert([
            'name' => 'Soldier T1',
            'nick' => 'Soldier',
            'description' => 'Texto de descrição do T1',
            'image' => 'droid-01.png',
            'type' => "droid",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('ships')->insert([
            'name' => 'Marine FFR02',
            'nick' => 'Marine',
            'description' => 'Texto de descrição do FFR02',
            'image' => 'droid-02.png',
            'type' => "droid",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('ships')->insert([
            'name' => 'Sentinela BALT3',
            'nick' => 'Sentinela',
            'description' => 'Texto de descrição do BALT3',
            'image' => 'droid-03.png',
            'type' => "droid",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('ships')->insert([
            'name' => 'Trooper Archer ARW4',
            'nick' => 'Trooper',
            'description' => 'Texto de descrição do ARW4',
            'image' => 'droid-04.png',
            'type' => "droid",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('ships')->insert([
            'name' => 'Laucher CMC5',
            'nick' => 'Laucher',
            'description' => 'Texto de descrição do CMC5',
            'image' => 'droid-05.png',
            'type' => "droid",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('ships')->insert([
            'name' => 'Bomber PHP6',
            'nick' => 'Bomber',
            'description' => 'Texto de descrição do PHP6',
            'image' => 'droid-06.png',
            'type' => "droid",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('ships')->insert([
            'name' => 'Screw Laucher SWL123',
            'nick' => 'Screw',
            'description' => 'Texto de descrição do SWL123',
            'image' => 'droid-07.png',
            'type' => "especial",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('ships')->insert([
            'name' => 'Killer Drone KD250',
            'nick' => 'Killer',
            'description' => 'Texto de descrição do KD250',
            'image' => 'droid-08.png',
            'type' => "especial",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('ships')->insert([
            'name' => 'MovTermShield MTS20',
            'nick' => 'MovShield',
            'description' => 'Texto de descrição do MTS20',
            'image' => 'droid-09.png',
            'type' => "vehicle",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('ships')->insert([
            'name' => 'Energy Claw ENC30',
            'nick' => 'Claw',
            'description' => 'Texto de descrição do ENC30',
            'image' => 'droid-10.png',
            'type' => "vehicle",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('ships')->insert([
            'name' => 'Glower Sky GSY40',
            'nick' => 'Glower',
            'description' => 'Texto de descrição do GSY40',
            'image' => 'droid-11.png',
            'type' => "launcher",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('ships')->insert([
            'name' => 'Rocket Rainer RR50',
            'nick' => 'Rocket',
            'description' => 'Texto de descrição do RR50',
            'image' => 'droid-12.png',
            'type' => "launcher",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);
    }
}
