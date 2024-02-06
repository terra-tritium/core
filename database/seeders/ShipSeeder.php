<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShipSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        
        // DB::table('ships')->insert([
        //     'name' => 'Rocket Rainer RR50',
        //     'nick' => 'Rocket',
        //     'description' => 'Texto de descrição do RR50',
        //     'image' => 'droid-12.png',
        //     'type' => "launcher",
        //     'defense' => 5,
        //     'attack' => 5,
        //     'life' => 100,
        //     'metal' => 500,
        //     'uranium' => 0,
        //     'crystal' => 0,
        //     'time' => 5
        // ]);
        DB::table('builds')->where('name','Shipyard')->update(
            ['effect' => 'Choose your starships wisely, customize their settings, and lead your faction to supremacy in the ever-expanding universe of Terra Tritium. The fate of your faction rests among the stars — command your fleet and forge your destiny among the cosmos!']
        );
        

        // DB::table('unitsShipyard')->delete();

        
        DB::table('ships')->insert([
            "name" => "Fighter Craft",
            "nick" => "Fighter",
            "description" => "Fast and agile, fighter crafts are ideal for short-range combat. They are often used to intercept other lighter ships or for quick attacks.",
            "image" => "ship-01.png",
            "type" => "Frontline",
            "defense" => 5,
            "attack" => 5,
            "speed" => 5,
            "size" => 5,
            "life" => 50,
            "metal" => 5000,
            "uranium" => 0,
            "crystal" => 0,
            "time"=> 5
        ]);
 

        DB::table('ships')->insert([
            "name" => "Heavy Bomber",
            "nick" => "Bomber",
            "description" => "Designed to attack fortified targets or capital ships, heavy bombers carry powerful armament to destroy larger and more protected targets.",
            "image" => "ship-02.png",
            "type" => "LongRange",
            "defense" => 5,
            "attack" => 20,
            "speed" => 4,
            "size" => 7,
            "life" => 80,
            "metal" => 15000,
            "uranium" => 0,
            "crystal" => 0,
            "time"=> 25
        ]);

        DB::table('ships')->insert([
            "name" => "Battle Cruiser",
            "nick" => "Cruiser",
            "description" => "Medium-sized ships, battle cruisers are versatile and can be used for both defense and offense. They usually have a balanced combination of firepower and resilience.",
            "image" => "ship-03.png",
            "type" => "Frontline",
            "defense" => 3,
            "attack" => 10,
            "speed" => 7,
            "size" => 7,
            "life" => 80,
            "metal" => 25000,
            "uranium" => 10000,
            "crystal" => 0,
            "time"=> 25
        ]);

        DB::table('ships')->insert([
            "name" => "Scout Walker 7",
            "nick" => "Scout",
            "description" => "Swift and agile, scout-class starships are the trailblazers of the cosmos. Ideal for scouting uncharted territories, these vessels excel at reconnaissance, evasion, and gathering vital intelligence. Uncover the mysteries of the universe with these sleek and nimble explorers.",
            "image" => "ship-04.png",
            "type" => "Frontline",
            "defense" => 5,
            "attack" => 5,
            "speed" => 20,
            "size" => 5,
            "life" => 50,
            "metal" => 50000,
            "uranium" => 0,
            "crystal" => 0,
            "time"=> 30
        ]);

        DB::table('ships')->insert([
            "name" => "Stealth Ship",
            "nick" => "Stealth",
            "description" => "Using advanced camouflage technology, stealth ships can approach enemies without being detected. They are ideal for ambushes and surprise attacks.",
            "image" => "ship-05.png",
            "type" => "Special",
            "defense" => 30,
            "attack" => 5,
            "speed" => 5,
            "size" => 5,
            "life" => 100,
            "metal" => 50000,
            "uranium" => 10000,
            "crystal" => 25000,
            "time"=> 100
        ]);

        DB::table('ships')->insert([
            "name" => "Flagship",
            "nick" => "Flagship",
            "description" => "The flagship is the largest and most powerful in the fleet. Besides being a command platform, it often possesses massive firepower and is crucial for fleet leadership and stability.",
            "image" => "ship-06.png",
            "type" => "Command",
            "defense" => 25,
            "attack" => 50,
            "speed" => 2,
            "size" => 50,
            "life" => 250,
            "metal" => 350000,
            "uranium" => 500000,
            "crystal" => 50000,
            "time"=> 250
        ]);    
    }
}
