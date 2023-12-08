<?php

namespace Database\Seeders;

use App\Models\AlianceRanking;
use App\Models\Debug;
use App\Models\Effect;
use App\Models\Planet;
use App\Models\Player;
use App\Models\User;
use App\Services\PlayerService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PlanetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $numQuadrante = 100;
        $numPlanetasPorRegiao = 17;
        for ($regiao = 'A'; $regiao <= 'P'; $regiao++) {
            for ($quadrant = 0; $quadrant < $numQuadrante; $quadrant++) {
                $planets = [];
                for ($i = 1; $i <= $numPlanetasPorRegiao; $i++) {
                    $planet = new Planet([
                        "level" => 1,
                        "name" => "Colony " . $regiao . "." . str_pad($quadrant, 3, '0', STR_PAD_LEFT) . "." . $i,
                        "region" => $regiao,
                        "quadrant" => $regiao . str_pad($quadrant, 3, '0', STR_PAD_LEFT),
                        "position" => $i,
                        "workers" => 30,
                        "workersWaiting" => 30,
                        "workersOnMetal" => 0,
                        "workersOnUranium"  => 0,
                        "workersOnCrystal" => 0,
                        "workersOnLaboratory" => 0,
                        "useEnergyByFactory" => 0,
                        "status" => "pacific",
                        "metal" => 1500,
                        "uranium" => 0,
                        "crystal" => 0,
                        "energy" => 1,
                        "battery" => 10000,
                        "extraBattery" => 0,
                        "capMetal" => 10000,
                        "capUranium" => 10000,
                        "capCrystal" => 10000,
                        "proMetal" => 1000,
                        "proUranium" => 1000,
                        "proCrystal" => 1000,
                        "pwMetal" => 0,
                        "pwUranium" => 0,
                        "pwCrystal" => 0,
                        "pwEnergy" => 0,
                        "pwWorker" => 0,
                        "transportShips" => 0,
                        "researchPoints" => 0,
                        "pwResearch" => 0,
                    ]);


                    /**Calculo type, resource e terreno */
                    $regionIndex = (ord($regiao) - ord('A'));
                    $terrainTypes = ["Desert", "Grass", "Ice", "Vulcan"];
                    $terrainIndex = $regionIndex % 4;

                    $planet->terrainType = $terrainTypes[$terrainIndex];
                    $planet->resource = ($regionIndex % 8 < 4) ? "uranium" : "crystal";

                    switch (true) {
                        case ($i > 0 && $i <= 4):
                            $planet->type = 1;
                            break;
                        case ($i >= 5 && $i <= 8):
                            $planet->type = 2;
                            break;
                        case ($i >= 9 && $i <= 12):
                            $planet->type = 3;
                            break;
                        default:
                            $planet->type = 4;
                            break;
                    }
                    $planets[] = $planet->toArray();
                    // $planet->save();
                }
                Planet::insert($planets);
                $planets = [];
            }
        } 
    }
}
