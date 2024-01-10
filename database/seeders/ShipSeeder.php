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
        DB::table('builds')->where('id','9')->update(
            ['effect' => 'Escolha suas naves estelares sabiamente, personalize suas configurações e conduza sua facção à supremacia no sempre expansivo universo de Terra Tritium. O destino de sua facção repousa nas estrelas — comande sua frota e forje seu destino entre os cosmos!']
        );
        

        // DB::table('unitsShipyard')->delete();

        
        DB::table('ships')->insert([
            "name" => "Dark Sun Troopship DST22",
            "nick" => "Dark",
            "description" => "Troop Transport-class Conveyors are the backbone of ground-based campaigns, ensuring your faction's dominance through strategic mobility and efficient deployment. Secure new frontiers, protect vital assets, and conquer the unknown with the dependable and adaptable Troop Transport-class Conveyors in Terra Tritium.",
            "image" => "ship-01.png",
            "type" => "fleet",
            "defense" => 5,
            "attack" => 5,
            "life" => 100,
            "metal" => 4000,
            "uranium" => 500,
            "crystal" => 300,
            "time"=> 250
        ]);
 

        DB::table('ships')->insert([
            "name" => "Star Camel Tradewing SCT09 ",
            "nick" => "Star",
            "description" => "Cargo Freighter-class Haulers are the economic backbone of your faction, facilitating trade and prosperity as you navigate the intricate web of commerce in Terra Tritium. Embark on profitable ventures, establish lucrative trade routes, and watch your faction's wealth soar with the reliable Cargo Freighter-class Haulers at the forefront of your economic endeavors.",
            "image" => "ship-02.png",
            "type" => "fleet",
            "defense" => 5,
            "attack" => 5,
            "life" => 100,
            "metal" => 3000,
            "uranium" => 1000,
            "crystal" => 300,
            "time"=> 200
        ]);

        DB::table('ships')->insert([
            "name" => "GKhan Crusier LN77",
            "nick" => "GKhan",
            "description" => "As the vanguard of your fleet, Strike-class Assault Cruisers are the embodiment of offensive prowess, striking fear into the hearts of your foes. Deploy them strategically, unleash their devastating firepower, and assert your faction's dominance across the cosmic battlegrounds of Terra Tritium.",
            "image" => "ship-03.png",
            "type" => "fleet",
            "defense" => 5,
            "attack" => 5,
            "life" => 100,
            "metal" => 5000,
            "uranium" => 2500,
            "crystal" => 300,
            "time"=> 250
        ]);

        DB::table('ships')->insert([
            "name" => "Walker 07 Scout Ship",
            "nick" => "Walker",
            "description" => "Swift and agile, scout-class starships are the trailblazers of the cosmos. Ideal for scouting uncharted territories, these vessels excel at reconnaissance, evasion, and gathering vital intelligence. Uncover the mysteries of the universe with these sleek and nimble explorers.",
            "image" => "ship-04.png",
            "type" => "fleet",
            "defense" => 5,
            "attack" => 5,
            "life" => 100,
            "metal" => 2000,
            "uranium" => 500,
            "crystal" => 500,
            "time"=> 100
        ]);

        DB::table('ships')->insert([
            "name" => "Starfinder Settler ZKO10",
            "nick" => "Starfinder",
            "description" => "Colony Transport-class Settlers are the first chapter in the narrative of your faction's expansion, carrying the hopes and dreams of colonists to the far reaches of Terra Tritium. Command these vessels wisely, choose colony sites strategically, and witness the flourishing of new civilizations under the banner of your faction.",
            "image" => "ship-05.png",
            "type" => "fleet",
            "defense" => 5,
            "attack" => 5,
            "life" => 100,
            "metal" => 5000,
            "uranium" => 2500,
            "crystal" => 2500,
            "time"=> 250
        ]);

        DB::table('ships')->insert([
            "name" => "Missile GKN17",
            "nick" => "Missile",
            "description" => "warfare. Deploy them strategically, exploit the weaknesses of your adversaries, and witness the skies ablaze as these precision missiles relentlessly pursue and annihilate enemy starships in the ongoing cosmic struggle for supremacy.",
            "image" => "ship-06.png",
            "type" => "fleet",
            "defense" => 5,
            "attack" => 5,
            "life" => 100,
            "metal" => 10000,
            "uranium" => 5000,
            "crystal" => 5000,
            "time"=> 500
        ]);    
    }
}
