<?php

namespace App\Services;

use App\Models\Travel;
use App\Jobs\TravelJob;
use App\Models\Position;
use App\Models\Planet;

class TravelService
{
    public function start ($player, $travel) {

        $newTravel = new Travel();

        if ($travel->action < 1 || $travel->action > 5) {
            return "Invalid action type";
        }

        if ((!isset($travel->from)) || (!isset($travel->to))) {
            return "Invalid locations";
        }

        if ($travel->from == $travel->to) {
            return "Impossible travel";
        }

        $now = time();
        $travelTime = env("TRITIUM_TRAVEL_SPEED") * $this->calcDistance($travel->from, $travel->to);
        $newTravel->from = $travel->from;
        $newTravel->to = $travel->to;
        $newTravel->action = $travel->action;
        $newTravel->player = $player;
        $newTravel->start = $now;
        $newTravel->arrival = $now + $travelTime;
        $newTravel->status = 1;
        $newTravel->receptor = $this->getReceptor($travel->to);

        # Troop
        if (isset($travel->troop)) {
            $newTravel->troop = $travel->troop;
        } else {
            $newTravel->troop = json_encode("{}");
        }
        # Fleet
        if (isset($travel->fleet)) {
            $newTravel->fleet = $travel->fleet;
        } else {
            $newTravel->fleet = json_encode("{}");
        }
        # Metal
        if (isset($travel->metal)) {
            $newTravel->metal = $travel->metal;
        } else {
            $newTravel->metal = 0;
        }
        # Crystal
        if (isset($travel->crystal)) {
            $newTravel->crystal = $travel->crystal;
        } else {
            $newTravel->crystal = 0;
        }
        # Uranium
        if (isset($travel->uranium)) {
            $newTravel->uranium = $travel->uranium;
        } else {
            $newTravel->uranium = 0;
        }
        # Merchant Ships
        if (isset($travel->transportShips)) {
            $newTravel->transportShips = $travel->transportShips;
        } else {
            $newTravel->transportShips = 0;
        }

        $newTravel->save();

        TravelJob::dispatch($newTravel->id)->delay(now()->addSeconds($travelTime / 1000));
    }

    public function back ($travel) {
        $now = time();
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
        $positionFrom = $this->convertPosition($from);
        $positionTo = $this->convertPosition($to);

        if (!($positionFrom && $positionTo)) {
            return false;
        }

        $diffRegion = abs(ord($positionFrom->region) - ord($positionTo->region));
        $diffQuadrant = abs($positionFrom->quadrant - $positionTo->quadrant);
        $diffPosition = abs($positionFrom->position - $positionTo->position);

        return ($diffRegion * 100) + ($diffQuadrant * 10) + $diffPosition;
    }

    public function convertPosition($location) {
        $position = new Position();
        $position->region = substr($location, 0, 1);
        $position->quadrant = substr($location, 1, 3);
        $l = explode(":", $location);
        $position->position = $l[1];
        
        if (!$this->isValidRegion($position->region)) { return false; }
        if (!$this->isValidQuadrant($position->quadrant)) { return false; }
        if (!$this->isValidPosition($position->position)) { return false; }
        
        return $position;
    }

    public function isValidRegion($letter) {
        $valorAscii = ord($letter);
        $valorAsciiA = ord('A');
        $valorAsciiP = ord('P');

        if ($valorAscii >= $valorAsciiA && $valorAscii <= $valorAsciiP) {
            return true;
        } else {
            return false;
        }
    }

    public function isValidQuadrant($quadrant) {
        if ($quadrant > 0 && $quadrant < 100) {
            return true;
        } else {
            return false;
        }
    }

    public function isValidPosition($position) {
        if ($position > 0 && $position <= 16) {
            return true;
        } else {
            return false;
        }
    }

    public function getReceptor($location) {
        $position = $this->convertPosition($location);
        $planet = Planet::where([
            ["quadrant", $position->quadrant],
            ["position", $position->position],
            ["region", $position->region]
        ])->first();
        if ($planet) {
            return $planet->player;
        } else {
            return 0;
        }
    }
}
