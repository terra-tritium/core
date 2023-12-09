<?php

namespace App\Services;

use App\Models\Planet;
use App\Models\Player;
use App\Models\Build;
use App\Models\Building;
use App\Models\Requires;
use App\Services\PlanetService;
use App\Services\PlayerService;
use App\Services\ResearchService;
use App\Services\WorkerService;

class BuildService
{
    private $basicScoreFator = 0.01;
    private $premiumScoreFator = 0.03;
    private $levelFactor = 100;
    private $initialBattery = 10000;

    public function __construct(
        private PlayerService $playerService = new PlayerService(),
        private PlanetService $planetService = new PlanetService(),
        private WorkerService $workerService = new WorkerService(),
        private ResearchService $researchService = new ResearchService()
    ) {}

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
            $planet = $this->planetService->startMining($p1, $resourceMining);
            $planet->save();
        }
    }

    public function plant($building) {

        $building->level = 1;
        $building->workers = 0;

        $p1 = Planet::find($building->planet);
        $build = Build::find($building->build);
        $playerLogged = Player::getPlayerLogged();
        $player = Player::findOrFail($playerLogged->id);

        # Yet have a building in construction on this planet
        if ($p1->ready != null && $p1->ready > time()) {
            return false;
        }

        $require = $this->calcResourceRequire($building->build, 1);

        $building->ready = time() + ($require->time * env("TRITIUM_BUILD_SPEED"));
        $p1->ready = $building->ready;

        // Colonization
        if ($building->build == 1) {
            if ($this->planetService->enoughBalance($p1, $require->metal, 1)) {
                $p1 = $this->planetService->removeMetal($p1, $require->metal);
                $p1->save();
            } else {
                return false;
            }
        }

        // Energy Collector
        if ($building->build == 2) {
            $this->starNewMining($p1, $building, 0, 1, $require->metal);
        }

        // Metal Mining
        if ($building->build == 4) {
            $this->starNewMining($p1, $building, 1, 1, $require->metal);
        }

        // Uranium Mining
        if ($building->build == 5) {
            if ($p1->resource != "uranium") {return false;}
            if (!$this->researchService->isResearched($player, 1300)) {
                return false;
            }
            $this->starNewMining($p1, $building, 2, 1, $require->metal);
        }

        // Crystal Mining
        if ($building->build == 6) {
            if ($p1->resource != "crystal") {return false;}
            if (!$this->researchService->isResearched($player, 1300)) {
                return false;
            }
            $this->starNewMining($p1, $building, 3, 1, $require->metal);
        }

        // Warehouse
        if ($building->build == 8) {
            if (!$this->researchService->isResearched($player, 1800)) {
                return false;
            }
        }

        // Shipyard
        if ($building->build == 9) {
            if (!$this->researchService->isResearched($player, 300)) {
                return false;
            }
        }

        // Battery House
        if ($building->build == 10) {
            if (!$this->researchService->isResearched($player, 2700)) {
                return false;
            }
        }

        // Military Camp
        if ($building->build == 11) {
            if (!$this->researchService->isResearched($player, 200)) {
                return false;
            }
        }

        // Shield
        if ($building->build == 12) {
            if (!$this->researchService->isResearched($player, 100)) {
                return false;
            }
            # Only construct allow in slot 4
            if ($building->slot != 4) {
                return false;
            }
        
            if ($this->planetService->enoughBalance($p1, $require->metal, 1)) {
                $p1 = $this->planetService->removeMetal($p1, $require->metal);
                $player = $this->playerService->addBuildScore($player, $require->metal * $this->basicScoreFator);
            } else {
                return false;
            }
        }

        // Market
        if ($building->build == 13) {
            if (!$this->researchService->isResearched($player, 1500)) {
                return false;
            }
        }

        // Diplomacy
        if ($building->build == 14) {
            if (!$this->researchService->isResearched($player, 400)) {
                return false;
            }
        }

        if ($building->build == 3 || $building->code > 6) {
            if ($this->planetService->enoughBalance($p1, $require->metal, 1)) {
                $p1 = $this->planetService->removeMetal($p1, $require->metal);
                $player = $this->playerService->addBuildScore($player, $require->metal * $this->basicScoreFator);
            } else {
                return false;
            }
            if ($this->planetService->enoughBalance($p1, $require->uranium, 2)) {
                $p1 = $this->planetService->removeUranium($p1, $require->uranium);
                $player = $this->playerService->addBuildScore($player, $require->uranium * $this->premiumScoreFator);
            } else {
                return false;
            }
            if ($this->planetService->enoughBalance($p1, $require->crystal, 3)) {
                $p1 = $this->planetService->removeCrystal($p1, $require->crystal);
                $player = $this->playerService->addBuildScore($player, $require->crystal * $this->premiumScoreFator);
            } else {
                return false;
            }
        }

        // Laboratory
        if ($building->build == Build::LABORATORY) {
            if ($this->planetService->enoughBalance($p1, $require->metal, 1)) {
                $p1 = $this->planetService->removeMetal($p1, $require->metal);
                $player = $this->playerService->addBuildScore($player, $require->metal * $this->basicScoreFator);
                $p1->pwEnergy = 1;
            } else {
                return false;
            }
        }

        $player = $this->playerService->addBuildScore($player, $this->levelFactor);

        $building->planet = Planet::where([['player', $player->id], ['id', $building->planet]])->firstOrFail()->id;

        $building->save();
        $p1->save();
        $player->save();
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

    public function spendResources(Planet $planet, $require) {
        $planet = $this->planetService->removeMetal($planet, $require->metal);
        $planet = $this->planetService->removeUranium($planet, $require->uranium);
        $planet = $this->planetService->removeCrystal($planet, $require->crystal);
        $planet->save();
    }

    public function updateScore(Player $player, $require) {
        $player = $this->playerService->addBuildScore($player, $require->metal * $this->basicScoreFator);
        $player = $this->playerService->addBuildScore($player, $require->uranium * $this->premiumScoreFator);
        $player = $this->playerService->addBuildScore($player, $require->crystal * $this->premiumScoreFator);
        $player->save();
    }

    public function demolish($buildId) {
        $building = Building::find($buildId);

        # Don't demolish all colonizators, have to have at least one
        if ($building->build == Build::COLONIZATION) {
            $countColonizator = Building::where('build', 1)->count();
            if ($countColonizator <= 1) {
                return false;
            }
        }

        if ($building->build == 4 || $building->build == 5 || $building->build == 6 || $building->build == 7) {
            $this->workerService->configWorkers($building->planet, 0, $building->id);
        }

        $building->delete();
    }

    public function upgrade($buildingId) {
        $building = Building::find($buildingId);
        $planet = Planet::find($building->planet);
        $player = Player::findOrFail($planet->player);
        $require = $this->calcResourceRequire($building->build, $building->level + 1);

        # Yet have a building in construction on this planet
        if ($planet->ready != null && $planet->ready > time()) {
            return false;
        }

        $building->ready = time() + ($require->time * env("TRITIUM_BUILD_SPEED"));
        $planet->ready = $building->ready;

        if ($this->suficientFunds($planet, $require)) {
            $this->spendResources($planet, $require);
            $this->updateScore($player, $require);
        } else {
            return false;
        }

        $building->level += 1;

        // Battery House
        if ($building->build == Build::BATERYHOUSE) {
            $this->planetService->incrementBattery($planet, $this->initialBattery * $building->level);
        }

        // Energy
        if ($building->build == Build::ENERGYCOLLECTOR) {
            $planet->pwEnergy = $building->level;
        }

        // Warehouse
        if ($building->build == Build::WAREHOUSE) {
            $planet->capMetal = $building->level * 10000;
            $planet->capUranium = $building->level * 10000;
            $planet->capCrystal = $building->level * 10000;
        }

        $player = $this->playerService->addBuildScore($player, $building->level * $this->levelFactor);

        $planet->save();
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

    public function listBuildings ($planet) {
        return Building::where("planet", $planet)->get();
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
            for ($i = 1; $i <= ($level - $build->metalLevel); $i++) {
                $metalReq += $metalReq * ($build->coefficient / 100);
            }
        }

        # Uranium
        if ($level == $build->uraniumLevel) {
            $uraniumReq = $build->uraniumStart;
        }
        if ($level > $build->uraniumLevel) {
            $uraniumReq = $build->uraniumStart;
            for ($i = 1; $i <= (($level - $build->uraniumLevel)); $i++) {
                $uraniumReq += $uraniumReq * ($build->coefficient / 100);
            }
        }

        # Crystal
        if ($level == $build->crystalLevel) {
            $crystalReq = $build->crystalStart;
        }
        if ($level > $build->uraniumLevel) {
            $crystalReq = $build->crystalStart;
            for ($i = 1; $i <= (($level - $build->crystalLevel)); $i++) {
                $crystalReq += $crystalReq * ($build->coefficient / 100);
            }
        }

        $require->metal = $metalReq;
        $require->uranium = $uraniumReq;
        $require->crystal = $crystalReq;
        $require->time = ($metalReq + $uraniumReq + $crystalReq) / 100;

        return $require;
    }
}
