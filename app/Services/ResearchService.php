<?php

namespace App\Services;

use App\Models\Researched;
use App\Models\Research;
use App\Services\PlayerService;

class ResearchService
{
    public function __construct () {
        $this->playerService = new PlayerService();
    }

    public function laboratoryConfig ($player, $planet, $power) {
        $this->playerSincronize($player);
        $planet->pwResearch = $power;
        $planet->save();
        return $planet;
    }

    public function buyResearch ($player, $research) {
        $this->playerSincronize($player);
        $researched = new Researched();
        $researched->player = $player->id;
        $researched->code = $research->code;
        $researched->save();
        $player->researchPoints -= $research->cost;
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
}
