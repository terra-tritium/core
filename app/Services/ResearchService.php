<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Researched;
use App\Models\Research;
use App\Models\Building;
use App\Models\Build;
use App\Services\PlayerService;
use App\Services\WorkerService;
use App\Services\BonusService;

class ResearchService
{
    public function __construct()
    {
        $this->playerService = new PlayerService();
        $this->workerService = new WorkerService();
        $this->bonusService = new BonusService();
    }

    public function laboratoryConfig($player, $planet, $power)
    {

        $planet->workersWaiting = $planet->workers - ($planet->workersOnMetal + $planet->workersOnUranium + $planet->workersOnCrystal + $power);

        # Don't have enough workers
        if ($planet->workersWaiting < 0) {
            return false;
        }

        $this->playerSincronize($player);
        $planet->pwResearch = $power;
        $planet->workersOnLaboratory = (int) $power;
        $planet->save();

        $this->workerService->syncronizeEnergy($planet);
        $this->updateBuildPower($planet->id, $power);

        return $planet;
    }

    public function buyResearch ($player, $research) {
        $this->playerSincronize($player);
        $researched = new Researched();
        $researched->player = $player->id;
        $researched->code = $research->code;
        $player->researchPoints -= $research->cost;

        # Don't have enough balance
        if ($player->researchPoints < 0) { return false; }

        $existsResearch = Researched::where([['player', $player->id], ['code', $research->code]])->first();

        # Already researched
        if ($existsResearch) { return false; }

        # Plasma
        if ($research->code == 500) {
            $this->bonusService->addPlasmaTechnology($player, 1);
        }

        # Hyperspeed
        if ($research->code == 600) {
            $this->bonusService->addSpeedTravel($player, 1);
        }

        # Future War 1
        if ($research->code == 1000) {
            $this->bonusService->addSpeedProduceUnit($player, 1);
        }

        # Future War 2
        if ($research->code == 1100) {
            $this->bonusService->addSpeedProduceUnit($player, 1);
        }

        # Future War 3
        if ($research->code == 1200) {
            $this->bonusService->addSpeedProduceUnit($player, 1);
        }

        # Energy Renewable
        if ($research->code == 1600) {
            $this->bonusService->addDiscountEnergy($player, 1);
        }

        # Factory
        if ($research->code == 1900) {
            $this->bonusService->addDiscountHumanoid($player, 1);
        }

        # Power Supply
        if ($research->code == 2100) {
            $this->bonusService->addDiscountEnergy($player, 1);
        }

        # Futere Economy 1
        if ($research->code == 2300) {
            $this->bonusService->addSpeedMining($player, 1);
        }

        # Futere Economy 2
        if ($research->code == 2400) {
            $this->bonusService->addSpeedMining($player, 1);
        }

        # Futere Economy 3
        if ($research->code == 2500) {
            $this->bonusService->addSpeedMining($player, 1);
        }

        # Ligth Reflexion
        if ($research->code == 2800) {
            $this->bonusService->addDiscountEnergy($player, 1);
        }

        # Locator
        if ($research->code == 3300) {
            $this->bonusService->addSpeedTravel($player, 1);
        }

        # Alien Technology
        if ($research->code == 3400) {
            $this->bonusService->addSpeedProduceShip($player, 1);
        }

        # Future Science 1
        if ($research->code == 3500) {
            $this->bonusService->addSpeedResearch($player, 1);
        }

        # Future Science 2
        if ($research->code == 3600) {
            $this->bonusService->addSpeedResearch($player, 1);
        }

        # Future Science 3
        if ($research->code == 3700) {
            $this->bonusService->addSpeedResearch($player, 1);
        }

        $points = $research->cost * 0.1;

        $player->score += $points;
        $player->researchScore += $points;

        $researched->save();
        $player->save();
        return $researched;
    }
    public function planetSincronize($planet) {
        $planet->researchPoints = ($planet->pwResearch * ((time() - $planet->timeResearch)/1000)) * config("app.tritium_research_speed");
        $planet->timeResearch = time();
        $planet->save();
        return $planet;
    }

    public function playerSincronize($player) {
        $points = 0;
        $planets = $this->playerService->getPlanets();
        foreach ($planets as $planet) {
            $p = $this->planetSincronize($planet);
            $points += $p->researchPoints;
        }
        $player->researchPoints += $points;
        $player->save();
    }


    public function updateBuildPower($planet, $power)
    {
        $building = Building::where([['planet', $planet], ['build', 8 /*ID LABORATORY*/]])->first();
        if ($building) {
            $building->workers = $power;
            $building->save();
        }
    }

    public function isResearched($player, $code)
    {
        $researched = Researched::where([['player', $player->id], ['code', $code]])->first();
        return $researched ? true : false;
    }
}
