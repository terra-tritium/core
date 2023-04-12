<?php

namespace App\Services;

use App\Models\Researched;
use App\Models\Research;
use Carbon\Carbon;

class ResearchService
{

    public function start ($player, $code) {

        $now = Carbon::now()->timestamp;

        $researchedStarted = Researched::where([["player", $player], ["status", 1]])->first();

        if ($researchedStarted) {
            if (!$this->isElegible($player,  $researchedStarted)) {
                return "not elegible";
            }
            $this->pause($researchedStarted);
            $researchedStarted->save();
        }

        $researched = Researched::where([["player", $player], ["code", $code]])->first();
        if ($researched) {
            # pause
            if ($researched->status == 0) {
                # start
                $researched->status = 1;
                $researched->timer = $now;
                $researched->save();
            }
        } else {

            $research = Research::where("code", $code)->firstOrFail();

            $researched = new Researched();
            $researched->player = $player;
            $researched->code = $code;
            $researched->points = 0;
            $researched->cost = $research->cost;
            $researched->power = 1;
            $researched->timer = $now;
            $researched->status = 1;
            $researched->save();
        }
    }

    public function pause ($researched) {
        $researched = $this->sincronize($researched);
        if ($researched->points >= $researched->cost) {
            # done
            $researched->status = 2;
        } else {
            # pause
            $researched->status = 0;
        }
        
        return $researched;
    }

    public function done ($player, $code) {
        $researched = Researched::where([["player", $player], ["code", $code]])->first();
        if ($researched) {
            if ($researched->points >= $researched->cost) {
                $researched->status = 2;
                $research->save();
            }
        }
    }

    public function sincronize ($researched) {
        $now = Carbon::now()->timestamp;
        $researched->points = floor((($now - $researched->timer) * 1000) / env("TRITIUM_RESEARCH_SPEED"));
        $researched->timer = $now;
        return $researched;
    }

    public function isElegible($player, $researched) {
        $research = Research::where("code", $researched->code)->firstOrFail();
        $dependences[] = explode(",", $research->dependence);

        $elegible = true;

        foreach($dependences as $dep) {
            if ($dep == 0) {
                return true;
            }
            $myResearched = Researched::where([["player", $player], ["code", $dep]])->first();
            if ($myResearched) {
                if (!$myResearched->status == 2) {
                    $elegible = false;
                }
            } else {
                $elegible = false;
            }
        }

        return $elegible;
    }
}