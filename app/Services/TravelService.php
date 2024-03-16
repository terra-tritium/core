<?php

namespace App\Services;

use App\Models\Travel;
use App\Models\Ship;
use App\Jobs\TravelJob;
use App\Models\Position;
use App\Models\Planet;
use App\Models\Troop;
use App\Models\Fleet;
use App\Models\Unit;
use App\Models\Player;
use App\Services\LogService;
use Carbon\Carbon;


class TravelService
{
    protected $planetService;
    protected $combatService;
    protected $logService;

    public function __construct( CombatService $combatService, PlanetService $planetService, LogService $logService)
    {
        $this->planetService =  $planetService ;
        $this->combatService =  $combatService  ;
        $this->logService =  $logService  ;
    }

    public function start ($player, $travel) {

        if (!is_object($travel)) {
            $travel =  json_decode (json_encode ($travel), FALSE);
        }
        
        $newTravel = new Travel();

        if ($travel->action < 1 || $travel->action > 8) {
            return "Invalid action type";
        }

        if ((!isset($travel->from)) || (!isset($travel->to))) {
            return "Invalid locations";
        }

        if ($travel->from == $travel->to) {
            return "Impossible travel";
        }

        if ($travel->action === Travel::ATTACK_TROOP && !isset($travel->troop))  {
            return "Set the troop";
        } elseif ($travel->action === Travel::ATTACK_TROOP) {
            if(!$this->hasTroopsAvailable($player, $travel->from, $travel->troop)){
                return "You don't have enough troops";
            }
        }

        if ($travel->action === Travel::ATTACK_FLEET && !isset($travel->fleet))  {
            return "Set the troop";
        } elseif ($travel->action === Travel::ATTACK_FLEET) {
            if(!$this->hasFleetAvailable($player, $travel->from, $travel->fleet)){
                return "You don't have enough ships";
            }
        }

        $now = time();
        $travelTime = $this->planetService->calculeDistance($travel->from, $travel->to);
        $newTravel->from = $travel->from;
        $newTravel->to = $travel->to;
        $newTravel->action = $travel->action;
        $newTravel->player = $player;
        $newTravel->start = $now;
        $newTravel->arrival = $now + $travelTime;
        $newTravel->receptor = $this->getReceptor($travel->to);
        $newTravel->strategy = $travel->strategy;
        $newTravel->transportShips = $travel->transportShip;
        $newTravel->status = Travel::STATUS_ON_LOAD;

        switch ($travel->action) {
            case Travel::ATTACK_FLEET:
                $newTravel = $this->startAttackFleet($newTravel, $travel, $player);
                $newTravel->status = Travel::STATUS_ON_GOING;
                $this->planetService->onFire($travel->to);
                break;
            case Travel::DEFENSE_FLEET:
                $newTravel = $this->startDefenseFleet($newTravel);
                break;
            case Travel::ATTACK_TROOP:
                $newTravel = $this->startAttackTroop($newTravel, $travel, $player);
                $this->planetService->onFire($travel->to);
                break;
            case Travel::DEFENSE_TROOP:
                $newTravel = $this->startDefenseTroop($newTravel);
                break;
            case Travel::TRANSPORT_RESOURCE:
                $newTravel = $this->startTransportResource($newTravel, $travel);
                $newTravel->status = Travel::STATUS_ON_GOING;
                break;
            case Travel::TRANSPORT_BUY:
                $newTravel = $this->startTransportBuy($newTravel);
                break;
            case Travel::TRANSPORT_SELL:
                $newTravel = $this->startTransportSell($newTravel);
                break;
            case Travel::MISSION_EXPLORER:
                $newTravel = $this->startMissionExplorer($newTravel);
                break;
            case Travel::RETURN_FLEET:
                // $newTravel = $this->startReturnFleet($newTravel, $travel, $player);
                // $this->planetService->offFire($travel->to);
                break;
        }

        # Merchant Ships
        if ($newTravel->transportShips < 0) {
            $newTravel->transportShips = 0;
        }

        $newTravel->save();
        TravelJob::dispatch($this,$newTravel->id, false)->delay(now()->addSeconds($travelTime));
    }

    private function startAttackFleet($travel, $req, $player) {

        $this->removeFleet($player, $req->from, $req->fleet);

        if (isset($req->fleet)) {
            foreach ($req->fleet as $ship) {
                switch ($ship->unit) {
                    case Ship::SHIP_CRAFT:
                        $travel->craft = $ship->quantity;
                        break;
                    case Ship::SHIP_BOMBER:
                        $travel->bomber = $ship->quantity;
                        break;
                    case Ship::SHIP_CRUISER:
                        $travel->cruiser = $ship->quantity;
                        break;
                    case Ship::SHIP_SCOUT:
                        $travel->scout = $ship->quantity;
                        break;
                    case Ship::SHIP_STEALTH:
                        $travel->stealth = $ship->quantity;
                        break;
                    case Ship::SHIP_FLAGSHIP:
                        $travel->flagship = $ship->quantity;
                        break;
                }
            }

        }
        return $travel;
    }

    private function startDefenseFleet($travel) {

    }

    private function startAttackTroop($travel, $req, $player) {

        $this->removeTroop($player, $req->from, $req->troop);

        if (isset($req->troop)) {
            $travel->troop = json_encode($req->troop);
        } else {
            $travel->troop = json_encode("{}");
        }
        return $travel;
    }

    private function startDefenseTroop($travel) {

    }

    private function startTransportResource($newTravel,$travel) {
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
        return $newTravel;
    }

    private function startTransportBuy($travel) {

    }

    private function startTransportSell($travel) {

    }

    private function startMissionExplorer($travel) {

    }

    public function back ($travel) {
        $now = time();

        $currentTravel = Travel::find($travel);
        $newTravel = $currentTravel->replicate();
        $travelTime = $now - $currentTravel->start;
        $newTravel->arrival = Carbon::now()->addSeconds($travelTime)->getTimestamp();
        //$newTravel->from  = $currentTravel->to ;
        //$newTravel->to     = $currentTravel->from ;
        $newTravel->status = Travel::STATUS_RETURN;
        $newTravel->push();

        TravelJob::dispatch($this,$newTravel->id,true)->delay(now()->addSeconds($travelTime));
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
        foreach($troops as $troop)
        {
            $troopModel = Troop::where(['unit' => $troop->unit, 'player' => $player, 'planet' => $planet])->first();

            if(!$troopModel) {
                return false;
            }
            if($troop->quantity > $troopModel->quantity)
            {
                return false;
            }
        }

        return true;
    }

    public function hasFleetAvailable($player, $planet, $fleets)
    {
        foreach($fleets as $fleet)
        {
            $fleetModel = Fleet::where(['unit' => $fleet->unit, 'player' => $player, 'planet' => $planet])->first();

            if(!$fleetModel) {
                return false;
            }
            if($fleet->quantity > $fleetModel->quantity)
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

        foreach($troops as $troop)
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

    public function removeFleet($player, $planet, $fleets){

        foreach($fleets as $fleet)
        {
            $fleetm = Fleet::where([
                'unit'      => $fleet->unit,
                'player'    => $player,
                'planet'    => $planet
            ])->first();

            $fleetm->quantity = ($fleetm->quantity -  $fleet->quantity);
            $fleetm->save();
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

        $missions = [];

        switch ($action) {
            case Travel::ATTACK_FLEET:
                $missions = $this->getMissionsByAction(Travel::ATTACK_FLEET);
                break;
            case Travel::DEFENSE_FLEET:
                $missions = $this->getMissionsByAction(Travel::DEFENSE_FLEET);
                break;
            case Travel::ATTACK_TROOP:
                $missions = $this->getMissionsByAction(Travel::ATTACK_TROOP);
                break;
            case Travel::DEFENSE_TROOP:
                $missions = $this->getMissionsByAction(Travel::DEFENSE_TROOP);
                break;
            case Travel::TRANSPORT_RESOURCE:
                $missions = $this->getMissionsByAction(Travel::TRANSPORT_RESOURCE);
                break;
            case Travel::TRANSPORT_BUY:
                $missions = $this->getMissionsByAction(Travel::TRANSPORT_BUY);
                break;
            case Travel::TRANSPORT_SELL:
                $missions = $this->getMissionsByAction(Travel::TRANSPORT_SELL);
                break;
            case Travel::MISSION_EXPLORER:
                $missions = $this->getMissionsByAction(Travel::MISSION_EXPLORER);
                break;
            case Travel::MISSION_SPIONAGE:
                $missions = $this->getMissionsByAction(Travel::MISSION_SPIONAGE);
                break;
            case "militar":
                $missions = Travel::with('from', 'to')
                    ->orWhere([['status', Travel::STATUS_ON_GOING], ['action', Travel::ATTACK_FLEET]])
                    ->orWhere([['status', Travel::STATUS_ON_GOING], ['action', Travel::DEFENSE_FLEET]])
                    ->orWhere([['status', Travel::STATUS_ON_GOING], ['action', Travel::ATTACK_TROOP]])
                    ->orWhere([['status', Travel::STATUS_ON_GOING], ['action', Travel::DEFENSE_TROOP]])
                    ->orWhere([['status', Travel::STATUS_ON_GOING], ['action', Travel::RETURN_FLEET]])
                    ->orWhere([['status', Travel::STATUS_ON_GOING], ['action', Travel::RETURN_TROOP]])
                    ->orWhere([['status', Travel::STATUS_ON_LOAD], ['action', Travel::ATTACK_FLEET]])
                    ->orWhere([['status', Travel::STATUS_ON_LOAD], ['action', Travel::DEFENSE_FLEET]])
                    ->orWhere([['status', Travel::STATUS_ON_LOAD], ['action', Travel::ATTACK_TROOP]])
                    ->orWhere([['status', Travel::STATUS_ON_LOAD], ['action', Travel::DEFENSE_TROOP]])
                    ->orWhere([['status', Travel::STATUS_ON_LOAD], ['action', Travel::RETURN_FLEET]])
                    ->orWhere([['status', Travel::STATUS_ON_LOAD], ['action', Travel::RETURN_TROOP]])
                    ->orderBy('arrival')
                    ->get();
                break;
        }

        return $missions;
    }

    private function getMissionsByAction($action) {
        return Travel::with('from', 'to')
                                        ->where([
                                                    ['action', $action],
                                                    ['status', Travel::STATUS_ON_LOAD],
                                                ])
                                        ->orderBy('arrival')->get();
    }

    public function starCombatTravel($travel)
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

        $this->combatService->startNewCombat($attack->id,  $defense->id, $aUnits, $dUnits, $aStrategy, $dStrategy, $dPlanet);
    }

    public function arrivedTransportResource($travel)
    {
        $travelModel = Travel::findOrFail($travel);
        $planetTarget = Planet::findOrFail($travelModel->to);

        $planetTarget->metal += $travelModel->metal;
        $planetTarget->uranium += $travelModel->uranium;
        $planetTarget->crystal += $travelModel->crystal;

        $planetTarget->save();
        $this->logService->notify($planetTarget->player, "You received a resource", "Mission");
        $this->back($travel);
    }

    public function arrivedTransportOrigin($travel)
    {
        $travelModel = Travel::findOrFail($travel);
        $planetOrige = Planet::findOrFail($travelModel->from);
        $planetOrige->transportShips += $travelModel->transportShips ;
        $planetOrige->save();
        $this->logService->notify($planetOrige->player, "Your freighter has returned from its trip", "Mission");
    }

    public function getCurrent($player)
    {
        $currentTravel = Travel::with('from', 'to')
                                ->where('player', $player)
                                ->whereIn('status',[2,3])->orderBy('arrival')->get();
        return  $currentTravel;
    }

    public function cancel($player,$travel)
    {
        $travelModel = Travel::where('player', $player)
                            ->where('id', $travel)
                            ->where('status',Travel::STATUS_ON_GOING)
                            ->first();

        if (is_null($travelModel)) {
            return false;
        }

        $travelModel->status = Travel::STATUS_CANCEL;
        $travelModel->save();

        $planetOrigim = Planet::findOrFail($travelModel->from);
        $planetOrigim->metal    += $travelModel->metal;
        $planetOrigim->uranium  += $travelModel->uranium;
        $planetOrigim->crystal  += $travelModel->crystal;
        $planetOrigim->transportShips += $travelModel->transportShips ;
        $planetOrigim->save();

        return true;
    }

    public function speyMission($player, $travel) {
        $travel =  json_decode (json_encode ($travel), FALSE);

        $travelModel = new Travel();
        $travelModel->action = Travel::MISSION_SPIONAGE;
        $travelModel->from = $travel->origin;
        $travelModel->to = $travel->destiny;

        $this->start($player,$travelModel);

        $typeMission = $travel->typeMission;
    }
}
