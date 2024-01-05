<?php

namespace App\Services;

use App\Models\Travel;
use App\Jobs\TravelJob;
use App\Models\Position;
use App\Models\Planet;
use App\Models\Troop;
use App\Models\Unit;
use App\Models\Player;

class TravelService
{
    private  $battleService;

    public function __construct(BattleService $battleService) {
        $this->battleService = $battleService;
        
    }

    public function start ($player, $travel) {
        
        $travel =  json_decode (json_encode ($travel), FALSE);
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
        
        if ($travel->action === 1 && !isset($travel->troop))  {
            return "Set the troop";
        } elseif ($travel->action === 1) {
            if(!$this->hasTroopsAvailable($player, $travel->from, $travel->troop)){
                return "You don't have enough troops";
            }
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
            $newTravel->troop = json_encode($travel->troop);
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
        $this->removeTroop($player, $travel->from, $travel->troop);
        TravelJob::dispatch($newTravel->id, $this)->delay(now()->addSeconds($travelTime));
    }

    public function back ($travel) {
        $now = time();

        $currentTravel = Travel::find($travel);
        $travelTime = $now - $currentTravel->start;
        $currentTravel->arrival = $travelTime;
        $currentTravel->status = 0;

        $newTravel = $currentTravel;
        $currentTravel->delete();
        $newTravel->save();

        TravelJob::dispatch($newTravel->id)->delay(now()->addSeconds($travelTime / 1000));
    }

    public function calcDistance($from, $to) {

        $planetFrom = Planet::where(['id' => $from])->firstOrFail();
        $planetTo = Planet::where(['id' => $to])->firstOrFail();

        $diffRegion = abs(ord($planetFrom->region) - ord($planetTo->region));
        $diffQuadrant = abs((int) $planetFrom->quadrant - (int) $planetTo->quadrant);
        $diffPosition = abs((int) $planetFrom->position - (int) $planetTo->position);

        return ($diffRegion * 100) + ($diffQuadrant * 10) + $diffPosition;
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
        if ($quadrant >= 0 && $quadrant < 100) {
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

    public function getReceptor($planetId) {
        $planet = Planet::where(['id' => $planetId])->firstOrFail();
        if ($planet) {
            return $planet->player;
        } else {
            return 0;
        }
    }

    public function hasTroopsAvailable($player, $planet, $troops)
    {
        foreach($troops as $key => $troop)
        {
            $troopModel = Troop::where(['unit' => $troop->unit, 'player' => $player, 'planet' => $planet])->first();

            if($troop->quantity > $troopModel->quantity)
            {
                return false;
            }
        }

        return true;
    }

    public function getPlanet($location) {
        $position = $this->convertPosition($location);
        $planet = Planet::where([
            ["quadrant", $position->quadrant_full],
            ["position", $position->position],
            ["region", $position->region]
        ])->first();
        
        if ($planet) {
            return $planet->id;
        } else {
            return 0;
        }
    }

    public function removeTroop($player, $planet, $troops){ 
        
        foreach($troops as $key => $troop)
        {  
            $troopm = Troop::where([
                'unit'      => $troop->unit,
                'player'    => $player,
                'planet'    => $planet
            ])->first();

            $troopm->quantity = ($troopm->quantity -  $troop->quantity);
            $troopm->save();
        }
    }

    public function getTroopAttack($travel){

        $travel = Travel::find($travel);
        $troops = json_decode($travel->troop);
        $units = [];

        foreach($troops as $key => $troop){

            $unit = Unit::find($troop->unit);
            $type = $this->getTypeUnit($unit->type);
            array_push($units, [
                'unit'=> $troop->unit,
                'quantity'=> $troop->quantity,
                'type'=> $type,
                'attack'=> $unit->attack,
                'defense'=> $unit->defense,
                'life'=> $unit->life
            ]);
        }

        return $units;
    }

    public function getTroopDefense($travel){
        $travel = Travel::find($travel);

        $troops = Troop::where('planet',$travel->receptor)->get();    
        $units = [];

        foreach($troops as $key => $troop){
            $unit = Unit::find($troop->unit);
            $type = $this->getTypeUnit($unit->type);
            array_push($units, [
                'unit'=> $troop->unit,
                'quantity'=> $troop->quantity,
                'type'=> $type,
                'attack'=> $unit->attack,
                'defense'=> $unit->defense ,
                'life'=> $unit->life
            ]);
        }

        return $units;
    }

    public function getTypeUnit($typeUnit){
        $type = '';
        switch($typeUnit){
            case 'droid':
                $type = 'D';
                break;
            case 'especial':
                $type = 'S';
                break;
            case 'vehicle':
                $type = 'V';
                break;
            case 'launcher':
                $type = 'L';
                break;
        }

        return $type;
    }

    /**
     * Get all missions by type
     */
    public function getMissions($action) {
        $missions = Travel::with('from', 'to')->where([['action', $action], ['status', 1]])->orderBy('arrival')->get();
        return $missions;
    }

    public function starBattleTravel($travel)
    {
        $travelModel = Travel::find($travel);
        
        $planet  = Planet::find($travelModel->receptor);

        $defense  = Player::find($planet->player);
        $attack  = Player::find($travelModel->player);

        $aUnits = $this->getTroopAttack($travel);
        $dUnits = $this->getTroopDefense($travel);
        $aStrategy = $attack->attackStrategy;
        $dStrategy = $defense->defenseStrategy;
        $dPlanet = $travelModel->receptor;

        $this->battleService->startNewBattle($attack->id,  $defense->id, $aUnits, $dUnits, $aStrategy, $dStrategy, $dPlanet);
    }

}
