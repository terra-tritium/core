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
        $numPlanetasPorRegiao = 15;
        for ($regiao = 'A'; $regiao <= 'P'; $regiao++) {
            for ($quadrant = 0; $quadrant < $numQuadrante; $quadrant++) {
                for ($i = 1; $i <= $numPlanetasPorRegiao; $i++) {
                    $planet = new Planet();
                    $planet->level = 1;
                    $planet->name = "Colony ".$regiao."".str_pad($quadrant, 3, '0', STR_PAD_LEFT).".".$i;
                    $planet->region = $regiao;
                    $planet->quadrant = $regiao . str_pad($quadrant, 3, '0', STR_PAD_LEFT);
                    $planet->position = $i;
                    // $planet->player = $player->id;
                    $planet->workers = 30;
                    $planet->workersWaiting = 30;
                    $planet->workersOnMetal = 0;
                    $planet->workersOnUranium = 0;
                    $planet->workersOnCrystal = 0;
                    $planet->workersOnLaboratory = 0;
                    $planet->useEnergyByFactory = 0;
                    $planet->status = "pacific";
                    $planet->metal = 1500;
                    $planet->uranium = 0;
                    $planet->crystal = 0;
                    $planet->energy = 1;
                    $planet->battery = 10000;
                    $planet->extraBattery = 0;
                    $planet->capMetal = 10000;
                    $planet->capUranium = 10000;
                    $planet->capCrystal = 10000;
                    $planet->proMetal = 1000;
                    $planet->proUranium = 1000;
                    $planet->proCrystal = 1000;
                    $planet->pwMetal = 0;
                    $planet->pwUranium = 0;
                    $planet->pwCrystal = 0;
                    $planet->pwEnergy = 0;
                    $planet->pwWorker = 0;
                    $planet->transportShips = 0;
                    $planet->researchPoints = 0;
                    $planet->pwResearch = 0;

                    /**Calculo type, resource e terreno */
                    $regionIndex = (ord($regiao) - ord('A'));
                    $terrainTypes = ["Desert", "Grass", "Ice", "Vulcan"];
                    $terrainIndex = $regionIndex % 4;
                    $planet->terrainType = $terrainTypes[$terrainIndex];

                    if ($regionIndex % 8 < 4) {
                        $planet->resource = "uranium";
                    } else {
                        $planet->resource = "crystal";
                    }
                    switch ($i) {
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
                    $planet->save();
                }
            }
        }
    }
}
