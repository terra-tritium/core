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
                        "name" => "Colony",
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
                        "researchPoints" => 0,
                        "pwResearch" => 0,
                    ]);


                    $regionIndex = ord($regiao);

                    $planet->resource = ($i % 2 == 0) ? "uranium" : "crystal";

                    $iQuadrant = substr($quadrant, -1);

                    switch (true) {
                        case ($iQuadrant == 0 || $iQuadrant == 4 || $iQuadrant == 8):
                            $planet->terrainType = "Desert";
                            break;
                        case ($iQuadrant == 1 || $iQuadrant == 5 || $iQuadrant == 9):
                            $planet->terrainType = "Grass";
                            break;
                        case ($iQuadrant == 2 || $iQuadrant == 6):
                            $planet->terrainType = "Ice";
                            break;
                        case ($iQuadrant == 3 || $iQuadrant == 7):
                            $planet->terrainType = "Vulcan";
                            break;
                    }

                    switch (true) {
                        case ($i == 1 || $i == 5 || $i == 9 || $i == 13):
                            $planet->type = 1;
                            break;
                        case ($i == 2 || $i == 6 || $i == 10 || $i == 14):
                            $planet->type = 2;
                            break;
                        case ($i == 3 || $i == 7 || $i == 11 || $i == 15):
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
