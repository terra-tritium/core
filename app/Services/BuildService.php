<?php

namespace App\Services;

use App\Models\Planet;
use App\Models\Player;
use App\Models\Build;
use App\Models\Building;
use App\Models\Requires;

use App\Services\PlayerService;

class BuildService
{
    private $playerService;

    public function __construct() {
        $this->playerService = new PlayerService();
    }

    private function starNewMining($p1, $building, $resourceMining, $resourceSpend, $require) {

        $hasBalance = false;

        switch ($resourceSpend) {
            case 1:
                $hasBalance = $playerService->enoughBalance($p1, $require, $resourceSpend);
                if ($hasBalance) { $p1 = $playerService->removeMetal($p1, $require); }
                break;
            case 2:
                $hasBalance = $playerService.enoughBalance($p1, $require, $resourceSpend);
                if ($hasBalance) { $p1 = $playerService->removeDeuterium($p1, $require); }
                break;
            case 3:
                $hasBalance = $playerService->enoughBalance($p1, $require, $resourceSpend);
                if ($hasBalance) { $p1 = $playerService->removeCrystal($p1, $require); }
                break;
        
            default:
                break;
        }

        if ($hasBalance) {
            $player = $playerService->startMining($p1, $resourceMining);
            $player->save();
        }
    }

    public function plant($building) {

        $building->level = 1;
        $building->workers = 0;

        $planet = Planet::find($building->planet);
        $p1 = Player::where("address", $planet->address)->firstOrFail();
        $build = Build::find($building->build);
        $require = Requires::find($building->require);

        # verify busy onBuilding
        if ($building.ready > time()) {
            return false;
        }

        $building->ready = time() + ($require->time * env(TRITIUM_BUILD_SPEED));

        // Colonization
        if ($building->code == 1) {
            if ($playerService->enoughBalance($p1, $require->metal, 1)) {
                $p1 = $playerService->removeMetal($p1, $require->metal);
                $p1.save();
            } else {
                return false;
            }
        }

        // Power Core
        if ($building->code == 2) {
            $this->starNewMining($p1, $building, 0, 1, $require->metal);
        }

        // Metal Mining
        if ($building.code == 4) {
            $this.starNewMining($p1, $building, 1, 1, $require->metal);
        }

        // Deuterium Mining
        if ($building.code == 5) {
            $this.starNewMining($p1, $building, 2, 1, $require->metal);
        }

        // Crystal Mining
        if ($building.code == 6) {
            $this.starNewMining($p1, $building, 3, 1, $require->metal);
        }

        if ($building.code == 3 || $building.code > 6) {
            if ($playerService->enoughBalance($p1, $require->metal, 1)) {
                $p1 = $playerService->removeMetal(p1, $require->metal);
            } else {
                return false;
            }
            if ($playerService->enoughBalance(p1, reqDeuterium, 2)) {
                $p1 = $playerService->removeDeuterium($p1, $require->deuterium);
            } else {
                return false;
            }
            if ($playerService.enoughBalance($p1, $require->crystal, 3)) {
                $p1 = $playerService.removeCrystal($p1, $require->crystal);
            } else {
                return false;
            }
        }
            
        $building->save();
        $p1.save();
        $planet.save();
    }

    public function suficientFunds($p1, $require) {
        if (!$playerService->enoughBalance($p1, $require->metal, 1)) {
            return false;
        }
        if (!$playerService->enoughBalance($p1, $require->deuterium, 2)) {
            return false;
        }
        if (!$playerService->enoughBalance($p1, $require->crystal, 3)) {
            return false;
        }
        return true;
    }

    public function spendResources($p1, $require) {
        $p1 = $playerService->removeMetal($p1, $require->metal);
        $p1 = $playerService->removeDeuterium($p1, $require->deuterium);
        $p1 = $playerService->removeCrystal($p1, $require->crystal);
        return $p1;
    }

    public function upgrade($buildingId) {

        $building = Building::find($buildingId);

        $planet = Planet::find($building->planet);
        $player = Player::where("address", $planet->address).firstOrFail();
        $build = Build::find($building->build);
        $require = Requires::where("id", $build->require)->andWhere("level", $building->level)->firstOrFail();

        $building->ready = time() + ($requrire.time * env("TRITIUM_BUILD_SPEED"));

        if ($this->suficientFunds($player, $require)) {
            $this->spendResources($player, $require);
        } else {
            return false;
        }

        $building->level += 1;

        $building.save();
    }

    public function listAvailableBuilds($planetId) {

        $allBuilds = Build::orderBy("code")->get();
        $buildeds = Building::where("planet", $planetId)->get();

        if (!$buildeds->isEmpty()) {
            foreach($buildeds as $iBuilded) {
                foreach($allBuilds as $iBuild) {
                    if ($iBuilded->code == $iBuild->code) {
                        $allBuilds->forget($iBuild->id);
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

        return $allBuilds;
    }

    public function configWorkers ($planetId, $workers, $buildingId) {

        $planet = Planet::find($planetId);
        $p1 = Player::where("address", $planet->address).firstOrFail();
        $buildings = Building::where("planet", $planet->id);
        
        if ($workers > $planet->humanoids || $workers < 1) {
            return 2;
        } else {

            foreach ($buildings as $iBuilding) {
                switch ($iBuilding->code) {
                    // Metal
                    case 4 : 
                        $p1->metal = $playerService->currentBalance($p1, 1);
                        $p1->timeMetal = time();
                        $p1->pwMetal = $workers;
                        break;

                    // Deuterium
                    case 5 : 
                        $p1->deuterium = $playerService.currentBalance($p1, 2);
                        $p1->timeDeuterium = time();
                        $p1->pwDeuterium = $workers;
                        break;

                    // Crystal
                    case 6 : 
                        $p1->crystal = $playerService.currentBalance($p1, 3);
                        $p1->timeCrystal = time();
                        $p1->pwCrystal = $workers;
                        break;
                }
            }
            
            $p1.save();
        }
    }
}