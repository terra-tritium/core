<?php

namespace App\Services;

use App\Modals\Travel;
use App\Jobs\TravelJob;
use Carbon\Carbon;

class TravelService
{
    public function start ($player, $travel) {

        $now = Carbon::now()->timestamp * 1000;
        $travelTime = env("TRITIUM_TRAVEL_SPEED") * $this->calcDistance($travel->from, $travel->to);

        $newTravel = new Travel();
        $newTravel->player = $player;
        $newTravel->receptor = $travel->receptor;
        $newTravel->from = $travel->from;
        $newTravel->to = $travel->to;
        $newTravel->troop = $travel->troop;
        $newTravel->fleet = $travel->fleet;
        $newTravel->metal = $travel->metal;
        $newTravel->crystal = $travel->crystal;
        $newTravel->uranium = $travel->uranium;
        $newTravel->start = $travel->now;
        $newTravel->action = $travel->action;
        $newTravel->arrival = $now + $travelTime;
        $newTravel->status = 1;
        $newTravel->merchantShips = $travel->merchantShips;
        $newTravel->save();

        TravelJob::dispatch($newTravel->id)->delay(now()->addSeconds($travelTime / 1000));
    }

    public function back ($travel) {
        $now = Carbon::now()->timestamp * 1000;
        $travelTime = $now - $currentTravel->start;

        $currentTravel = Travel::find($travel);
        $currentTravel->arrival = $travelTime;
        $currentTravel->status = 0;

        $newTravel = $currentTravel;
        $currentTravel->delete();
        $newTravel->save();

        TravelJob::dispatch($newTravel->id)->delay(now()->addSeconds($travelTime / 1000));
    }

    public function calcDistance($from, $to) {
        $diffRegion = abs($from->region - $$to->region);
        $diffQuadrant = abs($from->quadrant * $to->quadrant);

        return ($diffRegion * 10) + $diffQuadrant;
    }
}