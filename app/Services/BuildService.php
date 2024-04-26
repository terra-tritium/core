<?php

namespace App\Services;

use App\Models\Planet;
use App\Models\Player;
use App\Models\Build;
use App\Models\Building;
use App\Models\Effect;
use App\Models\GameMode;
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
        private ResearchService $researchService = new ResearchService(),
        private EffectService $effectService = new EffectService()
    ) {}

    private function starNewMining($p1, $building, $resourceMining, $resourceSpend, $require) {

        $buildEnergy = Building::where(['build' => Build::ENERGYCOLLECTOR, 'planet' => $p1->id])->first();

        if ($buildEnergy) {
            $levelEnergy = $buildEnergy->level;
        } else {
            $levelEnergy = 0;
        }

        $hasBalance = false;

        switch ($resourceSpend) {
            case 1:
                $hasBalance = $this->planetService->enoughBalance($p1, $require, $resourceSpend, $levelEnergy);
                if ($hasBalance) { $p1 = $this->planetService->removeMetal($p1, $require); }
                break;
            case 2:
                $hasBalance = $this->planetService->enoughBalance($p1, $require, $resourceSpend, $levelEnergy);
                if ($hasBalance) { $p1 = $this->planetService->removeUranium($p1, $require); }
                break;
            case 3:
                $hasBalance = $this->planetService->enoughBalance($p1, $require, $resourceSpend, $levelEnergy);
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

        $buildEnergy = Building::where(['build' => Build::ENERGYCOLLECTOR, 'planet' => $p1->id])->first();

        if ($buildEnergy) {
            $levelEnergy = $buildEnergy->level;
        } else {
            $levelEnergy = 0;
        }

        $build = Build::find($building->build);
        $playerLogged = Player::getPlayerLogged();
        $player = Player::findOrFail($playerLogged->id);
        $buildings  =  Building::where(['planet' => $building->planet, 'build' => $building->build])->first();

        if($buildings)
        {
            return false;
        }

        # Yet have a building in construction on this planet
        if ($p1->ready != null && $p1->ready > time()) {
            return false;
        }
 

        $require = $this->calcResourceRequire($building->build, 1, $player);
        $constructionSpeed = $this->effectService->calcConstructionBuildSpeed(config("app.tritium_construction_speed"),$player);
        
        $building->ready = time() + ($require->time * $constructionSpeed);
        $p1->ready = $building->ready;

        // Colonization
        if ($building->build == Build::COLONIZATION) {
            if ($this->planetService->enoughBalance($p1, $require->metal, 1, $levelEnergy)) {
                $p1 = $this->planetService->removeMetal($p1, $require->metal);
                $p1->save();
            } else {
                return false;
            }
        }

        // Energy Collector
        if ($building->build == Build::ENERGYCOLLECTOR) {
            $this->starNewMining($p1, $building, 0, 1, $require->metal);
        }

        // Humanoid Factory
        if ($building->build == Build::HUMANOIDFACTORY) {
            $this->starNewMining($p1, $building, 0, 1, $require->metal);
            $building->max_humanoids = 10;
        }

        // Metal Mining
        if ($building->build == Build::METALMINING) {
            $this->starNewMining($p1, $building, 1, 1, $require->metal);
        }

        // Uranium Mining
        if ($building->build == Build::URANIUMMINING) {
            if (!$this->researchService->isResearched($player, 1300)) {
                return false;
            }
            $this->starNewMining($p1, $building, 2, 1, $require->metal);
        }

        // Crystal Mining
        if ($building->build == Build::CRYSTALMINING) {
            if (!$this->researchService->isResearched($player, 1300)) {
                return false;
            }
            $this->starNewMining($p1, $building, 3, 1, $require->metal);
        }

        // Warehouse
        if ($building->build == Build::WAREHOUSE) {
            if (!$this->researchService->isResearched($player, 1800)) {
                return false;
            }
        }

        // Shipyard
        if ($building->build == Build::SHIPYARD) {
            if (!$this->researchService->isResearched($player, 300)) {
                return false;
            }
            $building->workers = 1;
        }

        // Battery House
        if ($building->build == Build::BATERYHOUSE) {
            if (!$this->researchService->isResearched($player, 2700)) {
                return false;
            }
        }

        // Military Camp
        if ($building->build == Build::MILITARYCAMP) {
            if (!$this->researchService->isResearched($player, 200)) {
                return false;
            }
        }

        // Shield
        if ($building->build == Build::SHIELD) {
            if (!$this->researchService->isResearched($player, 100)) {
                return false;
            }
            # Only construct allow in slot 4
            if ($building->slot != 4) {
                return false;
            }
        
            if ($this->planetService->enoughBalance($p1, $require->metal, 1, $levelEnergy)) {
                $p1 = $this->planetService->removeMetal($p1, $require->metal);
                $player = $this->playerService->addBuildScore($player, $require->metal * $this->basicScoreFator);
            } else {
                return false;
            }
        }

        // Market
        if ($building->build == Build::MARKET) {
            if (!$this->researchService->isResearched($player, 1500)) {
                return false;
            }
        }

        // Galatic Concil
        if ($building->build == Build::GALACTICCOUNCIL) {
            if (!$this->researchService->isResearched($player, 400)) {
                return false;
            }
        }

        if ($building->build == Build::COLONIZATION || $building->code > Build::METALMINING) {
            if ($this->planetService->enoughBalance($p1, $require->metal, 1, $levelEnergy)) {
                $p1 = $this->planetService->removeMetal($p1, $require->metal);
                $player = $this->playerService->addBuildScore($player, $require->metal * $this->basicScoreFator);
            } else {
                return false;
            }
            if ($this->planetService->enoughBalance($p1, $require->uranium, 2, $levelEnergy)) {
                $p1 = $this->planetService->removeUranium($p1, $require->uranium);
                $player = $this->playerService->addBuildScore($player, $require->uranium * $this->premiumScoreFator);
            } else {
                return false;
            }
            if ($this->planetService->enoughBalance($p1, $require->crystal, 3, $levelEnergy)) {
                $p1 = $this->planetService->removeCrystal($p1, $require->crystal);
                $player = $this->playerService->addBuildScore($player, $require->crystal * $this->premiumScoreFator);
            } else {
                return false;
            }
        }

        // Laboratory
        if ($building->build == Build::LABORATORY) {
            if ($this->planetService->enoughBalance($p1, $require->metal, 1, $levelEnergy)) {
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
        $buildEnergy = Building::where(['build' => Build::ENERGYCOLLECTOR, 'planet' => $p1->id])->first();

        if ($buildEnergy) {
            $levelEnergy = $buildEnergy->level;
        } else {
            $levelEnergy = 0;
        }

        if (!$this->planetService->enoughBalance($p1, $require->metal, 1, $levelEnergy)) {
            return false;
        }
        if (!$this->planetService->enoughBalance($p1, $require->uranium, 2, $levelEnergy)) {
            return false;
        }
        if (!$this->planetService->enoughBalance($p1, $require->crystal, 3, $levelEnergy)) {
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

    public function demolish($build,$planet) {
        $building = Building::where('build',$build)->where('planet',$planet)->firstOrFail();
        # Don't demolish all colonizators, have to have at least one
        if ($building->build == Build::COLONIZATION) {
            $countColonizator = Building::where('build', 1)->count();
            if ($countColonizator <= 1) {
                return false;
            }
        }

        if ($building->build == Build::METALMINING || $building->build == Build::URANIUMMINING || $building->build == Build::CRYSTALMINING || $building->build == Build::LABORATORY) {
            $this->workerService->configWorkers($building->planet, 0, $building->id);
        }
        return ["build"=>$build, "planetId"=>$planet];

        $building->delete();
    }

    public function upgrade($buildingId) {
        $building = Building::find($buildingId);
        $planet = Planet::find($building->planet);
        $player = Player::findOrFail($planet->player);

        
        $require = $this->calcResourceRequire($building->build, $building->level + 1, $player);

        # Yet have a building in construction on this planet
        if ($planet->ready != null && $planet->ready > time()) {
            return false;
        }
        
        $constructionSpeed = $this->effectService->calcConstructionBuildSpeed(config("app.tritium_construction_speed"),$player);
        $building->ready = time() + ($require->time * $constructionSpeed);
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

        // Humanoid Factory
        if ($building->build == Build::HUMANOIDFACTORY) {
            $building->max_humanoids = $building->level * 10;
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
    /**
     * @todo verificar a necessidade de utilizar
     * Aplicar efeito na listagem
     */
    public function applyDiscountBuild($planet,$builds){
        $planet = Planet::findOrFail($planet);
        $player = Player::findOrFail($planet->player);
        if($player->gameMode == 2){
            $effect = Effect::where('player',$player->id)->first();
            if($builds){
                $buildsDiscount = [];
                foreach($builds as $build){
                    $build->metalStart = floor($build->metalStart * (1 + ($effect->discountBuild / 100)));
                    $build->uraniumStart = floor($build->uraniumStart * (1 + ($effect->discountBuild / 100)));
                    $build->crystalStart = floor($build->crystalStart * (1 + ($effect->discountBuild / 100)));
                    $buildsDiscount[] = $build;
                }
                return $buildsDiscount;
            }

        }
        return $builds;

    }
    public function listAvailableBuilds($planet) {

        $allBuilds = Build::orderBy("code")->get();
        $buildings = Building::where("planet", $planet)->get();

        $availables = [];

        if (!$buildings->isEmpty()) {
            foreach($allBuilds as $key => $iBuild) {
                foreach($buildings as $iBuilding) {
                    if ($iBuilding->build == $iBuild->id) {
                        $iBuild->disable = true;
                    }
                }
            }
        }

        foreach($allBuilds as $temp) {

            # Disable all buildings if the planet is not colonized
            if (count($buildings) <= 0 && $temp->code != 1) {
                $temp->disable = true;
            }

            # Disable colonizator if the planet is colonized
            if (count($buildings) > 0 && $temp->code == 1) {
                $temp->disable = true;
            }

            array_push($availables, $temp);
        }

        return $availables;
    }

    public function listBuildings ($planet) {
        return Building::where("planet", $planet)->get();
    }

    public function calcResourceRequire($build, $level, $player) {
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
        // $require->metal = floor( $metalReq + (($metalReq * $discountBuild) / 100));
        $require->metal = $this->effectService->calcDiscountBuild($metalReq,$player);
        $require->uranium = $this->effectService->calcDiscountBuild($uraniumReq,$player);
        $require->crystal = $this->effectService->calcDiscountBuild($crystalReq,$player);
        // $require->uranium = $uraniumReq;

        $require->time = ($metalReq + $uraniumReq + $crystalReq) / 100;

        return $require;
    }
}
