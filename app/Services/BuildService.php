<?php

namespace App\Services;

use App\Models\Planet;
use App\Models\Player;
use App\Models\Build;
use App\Models\Building;
use App\Models\Requires;
use App\Services\PlayerService;

use Carbon\Carbon;

class BuildService
{
    private $playerService;

    private $basicScoreFator = 0.01;
    private $premiumScoreFator = 0.03;
    private $levelFactor = 100;
    private $initialBattery = 10000;

    public function __construct() {
        $this->playerService = new PlayerService();
    }

    private function starNewMining($p1, $building, $resourceMining, $resourceSpend, $require) {

        $hasBalance = false;

        switch ($resourceSpend) {
            case 1:
                $hasBalance = $this->playerService->enoughBalance($p1, $require, $resourceSpend);
                if ($hasBalance) { $p1 = $this->playerService->removeMetal($p1, $require); }
                break;
            case 2:
                $hasBalance = $this->playerService->enoughBalance($p1, $require, $resourceSpend);
                if ($hasBalance) { $p1 = $this->playerService->removeDeuterium($p1, $require); }
                break;
            case 3:
                $hasBalance = $this->playerService->enoughBalance($p1, $require, $resourceSpend);
                if ($hasBalance) { $p1 = $this->playerService->removeCrystal($p1, $require); }
                break;
        
            default:
                break;
        }

        if ($hasBalance) {
            $player = $this->playerService->startMining($p1, $resourceMining);
            $player->save();
        }
    }

    public function plant($building) {

        $building->level = 1;
        $building->workers = 0;

        $planet = Planet::find($building->planet);
        $p1 = Player::where("address", $planet->address)->firstOrFail();
        $build = Build::find($building->build);

        $require = Requires::where([
            ["build", "=", $building->build],
            ["level", "=", 1]
        ])->firstOrFail();

        $building->ready = (Carbon::now()->timestamp * 1000) + ($require->time * env("TRITIUM_BUILD_SPEED"));


        // Colonization
        if ($building->build == 1) {
            if ($this->playerService->enoughBalance($p1, $require->metal, 1)) {
                $p1 = $this->playerService->removeMetal($p1, $require->metal);
                $p1->save();
            } else {
                return false;
            }
        }

        // Power Core
        if ($building->build == 2) {
            $this->starNewMining($p1, $building, 0, 1, $require->metal);
        }

        // Metal Mining
        if ($building->build == 4) {
            $this->starNewMining($p1, $building, 1, 1, $require->metal);
        }

        // Deuterium Mining
        if ($building->build == 5) {
            $this->starNewMining($p1, $building, 2, 1, $require->metal);
        }

        // Crystal Mining
        if ($building->build == 6) {
            $this->starNewMining($p1, $building, 3, 1, $require->metal);
        }

        if ($building->build == 3 || $building->code > 6) {
            if ($this->playerService->enoughBalance($p1, $require->metal, 1)) {
                $p1 = $this->playerService->removeMetal($p1, $require->metal);
                $p1 = $this->playerService->addBuildScore($p1, $require->metal * $this->basicScoreFator);
            } else {
                return false;
            }
            if ($this->playerService->enoughBalance($p1, $require->deuterium, 2)) {
                $p1 = $this->playerService->removeDeuterium($p1, $require->deuterium);
                $p1 = $this->playerService->addBuildScore($p1, $require->deuterium * $this->premiumScoreFator);
            } else {
                return false;
            }
            if ($this->playerService->enoughBalance($p1, $require->crystal, 3)) {
                $p1 = $this->playerService->removeCrystal($p1, $require->crystal);
                $p1 = $this->playerService->addBuildScore($p1, $require->mecrystaltal * $this->premiumScoreFator);
            } else {
                return false;
            }
        }

        $p1 = $this->playerService->addBuildScore($p1, $this->levelFactor);
            
        $building->save();
        $p1->save();
        $planet->save();
    }

    public function suficientFunds($p1, $require) {
        if (!$this->playerService->enoughBalance($p1, $require->metal, 1)) {
            return false;
        }
        if (!$this->playerService->enoughBalance($p1, $require->deuterium, 2)) {
            return false;
        }
        if (!$this->playerService->enoughBalance($p1, $require->crystal, 3)) {
            return false;
        }
        return true;
    }

    public function spendResources($p1, $require) {
        $p1 = $this->playerService->removeMetal($p1, $require->metal);
        $p1 = $this->playerService->removeDeuterium($p1, $require->deuterium);
        $p1 = $this->playerService->removeCrystal($p1, $require->crystal);
        $p1 = $this->playerService->addBuildScore($p1, $require->metal * $this->basicScoreFator);
        $p1 = $this->playerService->addBuildScore($p1, $require->deuterium * $this->premiumScoreFator);
        $p1 = $this->playerService->addBuildScore($p1, $require->mecrystaltal * $this->premiumScoreFator);
        return $p1;
    }

    public function upgrade($buildingId) {

        $building = Building::find($buildingId);

        $planet = Planet::find($building->planet);
        $player = Player::where("address", $planet->address)->firstOrFail();
        $require = Requires::where([["build", $building->build], ["level", $building->level + 1]])->firstOrFail();

        $building->ready = (time() * 1000) + ($require->time * env("TRITIUM_BUILD_SPEED"));

        if ($this->suficientFunds($player, $require)) {
            $player = $this->spendResources($player, $require);
        } else {
            return false;
        }

        // Battery House
        if ($building->build == 11) {
            $player = $this->playerService->incrementBattery($p1, $this->initialBattery * $building->level);
        }

        $building->level += 1;

        $player = $this->playerService->addBuildScore($player, $building->level * $this->levelFactor);

        $player->save();
        $building->save();
    }

    public function listAvailableBuilds($planet) {

        $allBuilds = Build::orderBy("code")->get();
        $buildings = Building::where("planet", $planet)->get();

        $availables = [];

        if (!$buildings->isEmpty()) {
            foreach($allBuilds as $key => $iBuild) {
                foreach($buildings as $iBuilding) {
                    if ($iBuilding->build == $iBuild->code) {
                        $allBuilds->forget($key);
                    }
                }
            }
        } else {
            foreach($allBuilds as $key => $iBuild) {
                if ($iBuild->code != 1) {
                    $allBuilds->forget($key);
                }
            }
        }

        foreach($allBuilds as $temp) {
            array_push($availables, $temp);
        }

        return $availables;
    }

    public function listBildings ($planet) {
        return Building::where("planet", $planet)->get();
    }

    public function requires($build) {
        return Requires::where("build", $build)->get();
    }

    public function require($build, $level) {
        return Requires::where([["build", $build],["level", $level]])->firstOrFail();
    }

    public function configWorkers ($planetId, $workers, $buildingId) {

        $planet = Planet::find($planetId);
        $p1 = Player::where("address", $planet->address)->firstOrFail();
        $building = Building::find($buildingId);
        
        if ($workers > $planet->humanoids || $workers < 1) {
            return "Humanoids insuficients or workers invalid";
        } else {
        
            switch ($building->build) {
                // Metal
                case 4 : 
                    $p1->metal = $this->playerService->currentBalance($p1, 1);
                    $p1->timeMetal = time() * 1000;
                    $p1->pwMetal = $workers;
                    break;

                // Deuterium
                case 5 : 
                    $p1->deuterium = $this->playerService->currentBalance($p1, 2);
                    $p1->timeDeuterium = time() * 1000;
                    $p1->pwDeuterium = $workers;
                    break;

                // Crystal
                case 6 : 
                    $p1->crystal = $this->playerService->currentBalance($p1, 3);
                    $p1->timeCrystal = time() * 1000;
                    $p1->pwCrystal = $workers;
                    break;
            }

            $building->workers = $workers;
    
            $building->save();        
            $p1->save();
        }
    }
}