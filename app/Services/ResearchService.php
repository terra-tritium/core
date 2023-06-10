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

    // /**
    //  * @param int $player
    //  * @param int $code
    //  * @return string|void
    //  */
    // public function start (int $player, int $code, $sincronize = false) {

    //     $researchedStarted = Researched::where([["player", $player], ["status", 1]])->first();

    //     if ($sincronize) {
    //         $researchedSincronize = $this->sincronizeProgress($researchedStarted);
    //       //  dd($researchedSincronize);
    //         $researchedSincronize->save();
    //         return;
    //     }

    //     if ($researchedStarted) {
    //         if (!$this->isElegible($player,  $researchedStarted)) {
    //             return "not elegible";
    //         }
    //         $this->pause($researchedStarted);
    //         $researchedStarted->save();
    //     }

    //     $researched = Researched::where([["player", $player], ["code", $code]])->first();
    //     if ($researched) {
    //         # pause
    //         if ($researched->status == 0) {
    //             # start
    //             $researched->status = 1;
    //             $researched->timer = time();
    //             $researched->save();
    //         }
    //     } else {

    //         $research = Research::where("code", $code)->firstOrFail();

    //         $researched = new Researched();

    //         $researched->player = $player;
    //         $researched->code = $code;
    //         $researched->cost = $research->cost;
    //         $researched->power = 1;
    //         $researched->timer = time();
    //         $researched->start = time();
    //         $researched->status = 1;
    //         $researched->progress = 1;
    //         $researched->points = 0;
    //         $researched->finish = Carbon::now()->addMinutes($researched->cost / $researched->power)->timestamp;
    //         $researched->save();
    //     }
    // }

    // /**
    //  * @param $researched
    //  * @return mixed
    //  */
    // public function pause ($researched) {
    //     $researched = $this->sincronize($researched);
    //     if ($researched->points >= $researched->cost) {
    //         # done
    //         $researched->status = 2;
    //     } else {
    //         # pause
    //         $researched->status = 0;
    //     }

    //     return $researched;
    // }

    // /**
    //  * @param int $player
    //  * @param int $code
    //  * @return void
    //  */
    // public function done (int $player, int $code) {
    //     $researched = Researched::where([["player", $player], ["code", $code]])->first();
    //     if ($researched) {
    //         if ($researched->timer >= $researched->cost) {
    //             $researched->status = 2;
    //             $researched->save();
    //         }
    //     }
    // }

    // /**
    //  * @param $player
    //  * @param $code
    //  * @return mixed
    //  */
    // public function status($player, $code) {
    //     $researched = Researched::where([["player", $player], ["code", $code]])->first();
    //     return $researched->status;
    // }

    // /**
    //  * @param $researched
    //  * @return mixed
    //  */
    // public function sincronizeProgress($researched)
    // {
    //     $now = time();

    //     if ($now >= $researched->finish) {
    //         $researched->status = 3;
    //     } else {
    //         $researched->points = floor(($researched->finish - $now) / env("TRITIUM_RESEARCH_SPEED"));
    //     }

    //     return $researched;

    // }

    // /**
    //  * @param $researched
    //  * @return mixed
    //  */
    // public function sincronize ($researched) {
    //     $now = time();
    //     $researched->points = floor(($now - $researched->timer) / env("TRITIUM_RESEARCH_SPEED"));
    //     $researched->timer = $now;
    //     return $researched;
    // }

    // /**
    //  * @param int $player
    //  * @param $researched
    //  * @return bool
    //  */
    // public function isElegible(int $player, $researched) {
    //     $research = Research::where("code", $researched->code)->firstOrFail();
    //     $dependences[] = explode(",", $research->dependence);

    //     $elegible = true;

    //     foreach($dependences as $dep) {
    //         if ($dep == 0) {
    //             return true;
    //         }
    //         $myResearched = Researched::where([["player", $player], ["code", $dep]])->first();
    //         if ($myResearched) {
    //             if (!$myResearched->status == 2) {
    //                 $elegible = false;
    //             }
    //         } else {
    //             $elegible = false;
    //         }
    //     }

    //     return $elegible;
    // }
}
