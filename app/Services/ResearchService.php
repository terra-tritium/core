<?php

namespace App\Services;

use App\Models\Researched;
use App\Models\Research;
use App\Models\Building;
use App\Services\PlayerService;
use App\Services\WorkerService;

class ResearchService
{
    public function __construct () {
        $this->playerService = new PlayerService();
        $this->workerService = new WorkerService();
    }

    public function laboratoryConfig ($player, $planet, $power) {

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

        $researched->save();
        $player->save();
        return $researched;
    }

    public function planetSincronize($planet) {
        $planet->researchPoints = $planet->pwResearch * (time() - $planet->timeResearch) * env('TRITIUM_RESEARCH_SPEED');
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

    public function updateBuildPower($planet, $power) {
        $building = Building::where([['planet', $planet], ['build', 7]])->firstOrFail();
        $building->workers = $power;
        $building->save();
    }

    public function isResearched($player, $code) {
        $researched = Researched::where([['player', $player->id], ['code', $code]])->first();
        return $researched ? true : false;
    }
}
