<?php

namespace App\Services;

use App\Models\Planet;
use App\Models\Player;
use App\Models\Build;
use App\Models\Building;
use App\Models\Requires;
use App\Services\PlanetService;

use Carbon\Carbon;

class BuildService
{
    private $planetService;

    private $basicScoreFator = 0.01;
    private $premiumScoreFator = 0.03;
    private $levelFactor = 100;
    private $initialBattery = 10000;

    public function __construct() {
        $this->planetService = new PlanetService();
    }

    private function starNewMining($p1, $building, $resourceMining, $resourceSpend, $require) {

        $hasBalance = false;

        switch ($resourceSpend) {
            case 1:
                $hasBalance = $this->planetService->enoughBalance($p1, $require, $resourceSpend);
                if ($hasBalance) { $p1 = $this->planetService->removeMetal($p1, $require); }
                break;
            case 2:
                $hasBalance = $this->planetService->enoughBalance($p1, $require, $resourceSpend);
                if ($hasBalance) { $p1 = $this->planetService->removeUranium($p1, $require); }
                break;
            case 3:
                $hasBalance = $this->planetService->enoughBalance($p1, $require, $resourceSpend);
                if ($hasBalance) { $p1 = $this->planetService->removeCrystal($p1, $require); }
                break;
        
            default:
                break;
        }

        if ($hasBalance) {
            $player = $this->planetService->startMining($p1, $resourceMining);
            $player->save();
        }
    }

    public function plant($building) {

        $building->level = 1;
        $building->workers = 0;

        $p1 = Planet::find($building->planet); 
        $build = Build::find($building->build);

        $require = $this->calcResourceRequire($building->build, 1);

        $building->ready = (Carbon::now()->timestamp * 1000) + ($require->time * env("TRITIUM_BUILD_SPEED"));
        
        // Colonization
        if ($building->build == 1) {
            if ($this->planetService->enoughBalance($p1, $require->metal, 1)) {
                $p1 = $this->planetService->removeMetal($p1, $require->metal);
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

        // Uranium Mining
        if ($building->build == 5) {
            $this->starNewMining($p1, $building, 2, 1, $require->metal);
        }

        // Crystal Mining
        if ($building->build == 6) {
            $this->starNewMining($p1, $building, 3, 1, $require->metal);
        }

        if ($building->build == 3 || $building->code > 6) {
            if ($this->planetService->enoughBalance($p1, $require->metal, 1)) {
                $p1 = $this->planetService->removeMetal($p1, $require->metal);
                $p1 = $this->planetService->addBuildScore($p1, $require->metal * $this->basicScoreFator);
            } else {
                return false;
            }
            if ($this->planetService->enoughBalance($p1, $require->uranium, 2)) {
                $p1 = $this->planetService->removeUranium($p1, $require->uranium);
                $p1 = $this->planetService->addBuildScore($p1, $require->uranium * $this->premiumScoreFator);
            } else {
                return false;
            }
            if ($this->planetService->enoughBalance($p1, $require->crystal, 3)) {
                $p1 = $this->planetService->removeCrystal($p1, $require->crystal);
                $p1 = $this->planetService->addBuildScore($p1, $require->crystal * $this->premiumScoreFator);
            } else {
                return false;
            }
        }

        $p1 = $this->planetService->addBuildScore($p1, $this->levelFactor);
        
        $building->planet = Planet::where([['player', $p1->id], ['id', $building->planet]])->firstOrFail()->id;

        $building->save();
        $p1->save();
    }

    public function suficientFunds($p1, $require) {
        if (!$this->planetService->enoughBalance($p1, $require->metal, 1)) {
            return false;
        }
        if (!$this->planetService->enoughBalance($p1, $require->uranium, 2)) {
            return false;
        }
        if (!$this->planetService->enoughBalance($p1, $require->crystal, 3)) {
            return false;
        }
        return true;
    }

    public function spendResources($p1, $require) {
        $p1 = $this->planetService->removeMetal($p1, $require->metal);
        $p1 = $this->planetService->removeUranium($p1, $require->uranium);
        $p1 = $this->planetService->removeCrystal($p1, $require->crystal);
        $p1 = $this->planetService->addBuildScore($p1, $require->metal * $this->basicScoreFator);
        $p1 = $this->planetService->addBuildScore($p1, $require->uranium * $this->premiumScoreFator);
        $p1 = $this->planetService->addBuildScore($p1, $require->crystal * $this->premiumScoreFator);
        return $p1;
    }

    public function upgrade($buildingId) {

        $building = Building::find($buildingId);

        $planet = Planet::find($building->planet);
        $player = Player::findOrFail($planet->player);
        $require = $this->calcResourceRequire($building->build, $building->level + 1);

        $building->ready = (time() * 1000) + ($require->time * env("TRITIUM_BUILD_SPEED"));

        if ($this->suficientFunds($player, $require)) {
            $player = $this->spendResources($player, $require);
        } else {
            return false;
        }

        // Battery House
        if ($building->build == 11) {
            $player = $this->planetService->incrementBattery($p1, $this->initialBattery * $building->level);
        }

        $building->level += 1;

        $player = $this->planetService->addBuildScore($player, $building->level * $this->levelFactor);

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
                        $iBuild->disable = true;
                    }
                }
            }
        }

        foreach($allBuilds as $temp) {

            if (count($buildings) <= 0 && $temp->code != 1) {
                $temp->disable = true;
            }

            array_push($availables, $temp);
        }

        return $availables;
    }

    public function listBildings ($planet) {
        return Building::where("planet", $planet)->get();
    }

    public function configWorkers ($planetId, $workers, $buildingId) {

        $planet = Planet::find($planetId);
        $building = Building::find($buildingId);
        
        if ($workers > $planet->workers || $workers < 1) {
            return "Humanoids insuficients or workers invalid";
        } else {
        
            switch ($building->build) {
                // Metal
                case 4 : 
                    $planet->metal = $this->planetService->currentBalance($planet, 1);
                    $planet->timeMetal = time() * 1000;
                    $planet->pwMetal = $workers;
                    break;

                // Uranium
                case 5 : 
                    $planet->uranium = $this->planetService->currentBalance($planet, 2);
                    $planet->timeUranium = time() * 1000;
                    $planet->pwUranium = $workers;
                    break;

                // Crystal
                case 6 : 
                    $planet->crystal = $this->planetService->currentBalance($planet, 3);
                    $planet->timeCrystal = time() * 1000;
                    $planet->pwCrystal = $workers;
                    break;
            }

            $building->workers = $workers;
    
            $building->save();        
            $planet->save();
        }
    }

    public function calcResourceRequire($build, $level) {
        $build = Build::find($build);
        $require = new Requires();
        $metalReq = 0;
        $uraniumReq = 0;
        $crystalReq = 0;

        # Metal
        if ($level == $build->metalLevel) {
            $metalReq = $build->metalStart;
        }
        if ($level > $build->metalLevel) {
            $metalReq = $build->metalStart;
            for ($i = 1; $i <= (($level - $build->metalLevel) -1); $i++) {
                $metalReq += $metalReq * ($build->coefficient / 100);
            }
        }

        # Uranium
        if ($level == $build->uraniumLevel) {
            $uraniumReq = $build->uraniumStart;
        }
        if ($level > $build->uraniumLevel) {
            $metalReq = $build->metalStart;
            for ($i = 1; $i <= (($level - $build->uraniumLevel) -1); $i++) {
                $uraniumReq += $uraniumReq * ($build->coefficient / 100);
            }
        }

        # Crystal
        if ($level == $build->uraniumLevel) {
            $uraniumReq = $build->uraniumStart;
        }
        if ($level > $build->uraniumLevel) {
            $metalReq = $build->metalStart;
            for ($i = 1; $i <= (($level - $build->uraniumLevel) -1); $i++) {
                $uraniumReq += $uraniumReq * ($build->coefficient / 100);
            }
        }

        $require->metal = $metalReq;
        $require->uranium = $uraniumReq;
        $require->crystal = $crystalReq;
        $require->time = ($metalReq + $uraniumReq + $crystalReq) / 100;

        return $require;
    }
}