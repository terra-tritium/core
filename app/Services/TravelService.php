<?php

namespace App\Services;

use App\Models\Travel;
use App\Models\Ship;
use App\Jobs\TravelJob;
use App\Models\Espionage;
use App\Models\Planet;
use App\Models\Troop;
use App\Models\Fleet;
use App\Models\Unit;
use App\Models\Player;
use App\Models\Aliance;
use App\Services\LogService;
use App\Services\PlanetService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


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

        if (($travel->action < 1 || $travel->action > 11) && $travel->action != 18) {
            return "Invalid action type";
        }

        if ((!isset($travel->from)) || (!isset($travel->to))) {
            return "Invalid locations";
        }

        if ($travel->from == $travel->to) {
            return "Impossible travel";
        }

        $isAlienTravel = $this->isAlienAtack($travel->to);

        if ($isAlienTravel && ($travel->action != Travel::ATTACK_FLEET)) {
            return "Action no permission for alien travel";
        }

        if (!$this->validateTransportShip($player, $travel->transportShips)) {
            return "You don't have enough transport ships";
        }

        if ($travel->action === Travel::ATTACK_TROOP && !isset($travel->troop))  {
            return "Set the troop";
        } elseif ($travel->action === Travel::ATTACK_TROOP) {
            if(!$this->hasTroopsAvailable($player, $travel->from, $travel->troop)){
                return "You don't have enough troops";
            }
        }

        if ($travel->action === Travel::ATTACK_FLEET && !isset($travel->fleet))  {
            return "Set the fleet";
        } elseif ($travel->action === Travel::ATTACK_FLEET) {
            if(!$this->hasFleetAvailable($player, $travel->from, $travel->fleet)){
                return "You don't have enough ships";
            }
        }

        if ($travel->action === Travel::DEFENSE_FLEET && !isset($travel->fleet))  {
            return "Set the fleet";
        } elseif ($travel->action === Travel::DEFENSE_FLEET) {
            if(!$this->hasFleetAvailable($player, $travel->from, $travel->fleet)){
                return "You don't have enough ships";
            }
        }

        if ($travel->action === Travel::RETURN_FLEET) {
            if (!$this->validateReturnFleet($travel)) {
                return "You can't send a return fleet at moment";
            }
        }

        # Populate Travel
        $now = time();
        if ($isAlienTravel) {
            $travelTime = 300;
        } else {
            $travelTime = $this->planetService->calculeDistance($travel->from, $travel->to);
        }
        if ($this->isJumpActivate($player)) {
            $travelTime = 10;
        }
        $newTravel->from = $travel->from;
        $newTravel->to = $travel->to;
        $newTravel->action = $travel->action;
        $newTravel->player = $player;
        $newTravel->start = $now;
        $newTravel->arrival = $now + $travelTime;
        if (!$isAlienTravel) {
            $newTravel->receptor = $this->getReceptor($travel->to);
        } else {
            $newTravel->receptor = 0;
        }
        $newTravel->strategy = $travel->strategy;
        $newTravel->transportShips = $travel->transportShips;
        $newTravel->status = Travel::STATUS_ON_LOAD;

        switch ($travel->action) {
            case Travel::ATTACK_FLEET:
                $newTravel = $this->startAttackFleet($newTravel, $travel, $player);
                $newTravel->status = Travel::STATUS_ON_GOING;
                if (!$isAlienTravel) {
                    $this->planetService->onFire($travel->to);
                }
                break;
            case Travel::DEFENSE_FLEET:
                $newTravel = $this->startDefenseFleet($newTravel, $travel, $player);
                $newTravel->status = Travel::STATUS_ON_GOING;
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
                $this->planetService->offFire($travel->to);
                break;
            case Travel::MISSION_SPIONAGE:
                $newTravel->status = Travel::STATUS_ON_GOING;
                break;
            case Travel::DOMAIN_RETURN:
                $newTravel->status = Travel::STATUS_ON_GOING;
                break;

        }

        # Merchant Ships
        if ($newTravel->transportShips < 0 || is_null($newTravel->transportShips )) {
            $newTravel->transportShips = 0;
        }

        try {
            $newTravel->save();
        } catch(\Exception $exception) {
            Log::error('Erro ao executar gravar uma travel: ' . $exception->getMessage());
        }

        TravelJob::dispatch($this,$newTravel->id, false)->delay(now()->addSeconds($travelTime));

        return "success";
    }

    private function isJumpActivate($playerId) {
        $player = Player::find($playerId);
        if (empty($player->aliance)) {
            return false;
        }

        $umDia = 86400;
        $agora = time();

        $aliance = Aliance::find($player->aliance);

        # assegura validade de 24 horas de ativacao do efeito
        if ($aliance->jump > ($agora - $umDia)) {
            return true;
        }
        return false;
    }

    private function isAlienAtack($destiny) {
        $code = explode('#', $destiny)[0]; // pega só a parte antes do '#'
    
        return in_array($code, ["9996", "9997", "9998", "9999"]);
    }

    private function validateReturnFleet ($player) {
        $travelAtack = Travel::where([['player', $player], ['action', Travel::ATTACK_FLEET]])->orderBy('id', 'desc')->first();
        $travelReturn = Travel::where([['player', $player], ['action', Travel::RETURN_FLEET]])->orderBy('id', 'desc')->first();
        if ($travelAtack) {
            if (!$travelReturn) {
                return true;
            }
            if ($travelAtack->id > $travelReturn->id) {
                return true;
            }
        }
        return false;
    }

    private function removeTransportShips ($player, $qtd) {
        $player = Player::find($player);
        $player->transportShips -= $qtd;
        $player->save();
        return true;
    }

    private function validateTransportShip($playerId, $qtd) {
        $player = Player::find($playerId);
        if ($player->transportShips >= $qtd) {
            return true;
        } else {
            return false;
        }
    }

    private function startAttackFleet($travel, $req, $player) {

        $this->removeTransportShips($player, $req->transportShips);
        
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

    private function startDefenseFleet($travel, $req, $player) {
        
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

    private function startMissionEspioned($travel) {

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

    private function qtdMyPlanets($playerId) {
        return Planet::where('player', $playerId)->count();
    }

    public function colonizePlanet($planetId, $playerId) {

        $qtdPlanets = $this->qtdMyPlanets($playerId);

        $custMetal = 0;
        $custUranium = 0;
        $custCrystal = 0;

        if ($qtdPlanets <= 1) {
            $custMetal = 100000;
            $custUranium = 50000;
            $custCrystal = 50000;
        }

        if ($qtdPlanets == 2) {
            $custMetal = 200000;
            $custUranium = 100000;
            $custCrystal = 100000;
        }

        if ($qtdPlanets == 3) {
            $custMetal = 300000;
            $custUranium = 150000;
            $custCrystal = 150000;
        }

        if ($qtdPlanets == 4) {
            $custMetal = 500000;
            $custUranium = 250000;
            $custCrystal = 250000;
        }

        if ($qtdPlanets == 5) {
            $custMetal = 1000000;
            $custUranium = 500000;
            $custCrystal = 500000;
        }

        if ($qtdPlanets == 6) {
            $custMetal = 3000000;
            $custUranium = 1500000;
            $custCrystal = 1500000;
        }

        if ($qtdPlanets == 7) {
            $custMetal = 5000000;
            $custUranium = 2500000;
            $custCrystal = 2500000;
        }

        if ($qtdPlanets == 8) {
            $custMetal = 25000000;
            $custUranium = 5000000;
            $custCrystal = 5000000;
        }

        $totalPoints = ($custMetal + $custUranium + $custCrystal) / 100;

        $planetColony = Planet::find($planetId);
        $planetFrom = Planet::where(['player' => $playerId])->firstOrFail();
        $planetFrom->metal -= $custMetal;
        $planetFrom->uranium -= $custUranium;
        $planetFrom->crystal -= $custCrystal;

        if ($planetFrom->metal < 0 || $planetFrom->uranium < 0 || $planetFrom->crystal < 0) { return false;  }

        $planetFrom->save();

        $travel = new Travel();
        $travel->from = $planetFrom->id;
        $travel->to = $planetColony->id;
        $travel->action = Travel::MISSION_COLONIZATION;
        $travel->player = $playerId;
        $travel->receptor = $playerId;
        $travel->start = time();
        $travelTime = $this->planetService->calculeDistance($planetFrom->id, $planetColony->id);
        $travel->arrival = time() + $travelTime;
        $travel->status = Travel::STATUS_ON_LOAD;
        $travel->save();

        # adiciona a pontuacao ao jogador
        $player = Player::find($playerId);
        $player->score += $totalPoints;
        $player->save();

        TravelJob::dispatch($this, $travel->id, false)->delay(now()->addSeconds($travelTime));
    }

    public function missionColonization ($travel) {
        $planet = Planet::find($travel->to);
        $planet->player = $travel->player;
        $planet->metal = 1500;
        $planet->uranium = 0;
        $planet->crystal = 0;
        $planet->save();

        $logService = new LogService();
        $logService->notify(
          $travel->player,
          "The planet " . $travel->to . " was colonized",
          "Colonization"
        );
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
            if (!empty($planet->player)) {
                return $planet->player;
            } else {
                return 0;
            }
            
        } else {
            return 0;
        }
    }

    public function hasTroopsAvailable($player, $planet, $troops)
    {
        foreach($troops as $troop)
        {

            if ($troop->quantity <= 0) {
                return false;
            }

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
            if ($fleet->quantity <= 0) {
                return false;
            }

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

    public function addFleet($player, $planet, $fleets){

        foreach($fleets as $fleet)
        {
            $fleetm = Fleet::where([
                'unit'      => $fleet->unit,
                'player'    => $player,
                'planet'    => $planet
            ])->first();

            $fleetm->quantity = ($fleetm->quantity +  $fleet->quantity);
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
    public function getMissions($action, $player) {

        $missions = [];

        $planets = Planet::where('player', $player->id)->get();

        switch ($action) {
            case Travel::ATTACK_FLEET:
                $missions = $this->getMissionsByAction(Travel::ATTACK_FLEET, $player->id, $planets);
                break;
            case Travel::DEFENSE_FLEET:
                $missions = $this->getMissionsByAction(Travel::DEFENSE_FLEET, $player->id, $planets);
                break;
            case Travel::ATTACK_TROOP:
                $missions = $this->getMissionsByAction(Travel::ATTACK_TROOP, $player->id, $planets);
                break;
            case Travel::DEFENSE_TROOP:
                $missions = $this->getMissionsByAction(Travel::DEFENSE_TROOP, $player->id, $planets);
                break;
            case Travel::TRANSPORT_RESOURCE:
                $missions = $this->getMissionsByAction(Travel::TRANSPORT_RESOURCE, $player->id, $planets);
                break;
            case Travel::TRANSPORT_BUY:
                $missions = $this->getMissionsByAction(Travel::TRANSPORT_BUY, $player->id, $planets);
                break;
            case Travel::TRANSPORT_SELL:
                $missions = $this->getMissionsByAction(Travel::TRANSPORT_SELL, $player->id, $planets);
                break;
            case Travel::MISSION_EXPLORER:
                $missions = $this->getMissionsByAction(Travel::MISSION_EXPLORER, $player->id, $planets);
                break;
            case Travel::MISSION_SPIONAGE:
                $missions = $this->getMissionsByAction(Travel::MISSION_SPIONAGE, $player->id, $planets);
                break;
            case Travel::MISSION_COLONIZATION:
                $missions = $this->getMissionsByAction(Travel::MISSION_COLONIZATION, $player->id, $planets);
                break;
            case "militar":
                $missions = Travel::with('from', 'to')
                    ->where(function($query) use ($planets) {
                        foreach ($planets as $planet) {
                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::ATTACK_FLEET]]);
                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::DEFENSE_FLEET]]);
                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::ATTACK_TROOP]]);
                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::DEFENSE_TROOP]]);
                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::RETURN_FLEET]]);
                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::RETURN_TROOP]]);
                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::MISSION_CHALLANGE]]);
                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::RETURN_CHALLANGE]]);
                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::DEFENSE_RETURN]]);
                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::DOMAIN_RETURN]]);

                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_LOAD],  ['action', Travel::ATTACK_FLEET]]);
                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_LOAD],  ['action', Travel::DEFENSE_FLEET]]);
                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_LOAD],  ['action', Travel::ATTACK_TROOP]]);
                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_LOAD],  ['action', Travel::DEFENSE_TROOP]]);
                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_LOAD],  ['action', Travel::RETURN_FLEET]]);
                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_LOAD],  ['action', Travel::RETURN_TROOP]]);
                            $query->orWhere([['from', $planet->id], ['status', Travel::STATUS_ON_LOAD],  ['action', Travel::DOMAIN_RETURN]]);

                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::ATTACK_FLEET]]);
                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::DEFENSE_FLEET]]);
                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::ATTACK_TROOP]]);
                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::DEFENSE_TROOP]]);
                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::RETURN_FLEET]]);
                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::RETURN_TROOP]]);
                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::MISSION_CHALLANGE]]);
                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::RETURN_CHALLANGE]]);
                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::DEFENSE_RETURN]]);
                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_GOING], ['action', Travel::DOMAIN_RETURN]]);

                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_LOAD],  ['action', Travel::ATTACK_FLEET]]);
                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_LOAD],  ['action', Travel::DEFENSE_FLEET]]);
                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_LOAD],  ['action', Travel::ATTACK_TROOP]]);
                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_LOAD],  ['action', Travel::DEFENSE_TROOP]]);
                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_LOAD],  ['action', Travel::RETURN_FLEET]]);
                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_LOAD],  ['action', Travel::RETURN_TROOP]]);
                            $query->orWhere([['to', $planet->id], ['status', Travel::STATUS_ON_LOAD],  ['action', Travel::DOMAIN_RETURN]]);
                        }
                    })
                    ->orderBy('start', 'desc')
                    ->limit(20)
                    ->get();
                break;
        }

        return $missions;
    }

    private function getMissionsByAction($action, $playerId, $planets) {
        return Travel::with('from', 'to')
            ->where(function($query) use ($planets, $action) {
                foreach ($planets as $planet) {
                    $query->orWhere([['from', $planet->id], ['action', $action]]);
                    $query->orWhere([['to', $planet->id], ['action', $action]]);
                }
            })
            ->orWhere([['player', $playerId], ['action', $action]])
            ->orderBy('start', 'desc')
            ->limit(20)
            ->get();
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
        $this->logService->notify($planetTarget->player, "You received a resource: Metal ".$travelModel->metal." Uranium ".$travelModel->uranium." Crystal ".$travelModel->crystal, "Mission");
        $this->back($travel);
    }

    public function arrivedTransportOrigin($travel)
    {
        $travelModel = Travel::findOrFail($travel);
        $playerOrige = Player::findOrFail($travelModel->player);
        $playerOrige->transportShips += $travelModel->transportShips;
        $playerOrige->save();
        $this->logService->notify($playerOrige->id, "Your freighter has returned from its trip, TransportShips ".$travelModel->transportShips, "Mission");
    }

    public function getCurrent($player)
    {
        $currentTravel = Travel::with('from', 'to')
                                ->where(function ($query) use ($player) {
                                    $query->where('player', $player)
                                        ->orWhere('receptor', $player);
                                })
                                ->whereIn('status',[
                                    Travel::STATUS_ON_LOAD,
                                    Travel::STATUS_ON_GOING,
                                    Travel::STATUS_RETURN]
                                )->orderBy('arrival')->get();
        return  $currentTravel;
    }

    #TODO Validar quais tipos de viages podem ser canceladas
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

        switch($travel->action)
        {
            case Travel::TRANSPORT_RESOURCE:
                $planetOrigim = Planet::findOrFail($travelModel->from);
                $planetOrigim->metal    += $travelModel->metal;
                $planetOrigim->uranium  += $travelModel->uranium;
                $planetOrigim->crystal  += $travelModel->crystal;
                $planetOrigim->save();

                $playerOrigim = Player::findOrFail($planetOrigim->player);
                $playerOrigim->transportShips += $travelModel->transportShips ;
                $playerOrigim->save();

                break;

            case Travel::MISSION_SPIONAGE:
                $spyModel = Espionage::where('travel',$travel->id)->first();
                $spyModel->success = false;
                $spyModel->end_date = Carbon::now();
                $spyModel->finished = true;
                $spyModel->save();
                break;

            case Travel::ATTACK_FLEET : break;
            case Travel::ATTACK_TROOP : break;
            case Travel::DEFENSE_FLEET : break;
            case Travel::DEFENSE_TROOP : break;
            case Travel::TRANSPORT_BUY : break;
            case Travel::TRANSPORT_SELL : break;
            case Travel::MISSION_EXPLORER : break;
            case Travel::RETURN_FLEET : break;
            case Travel::RETURN_TROOP : break;
        }

        return true;
    }

    public function speyMission($player, $travel) {
        $travel =  json_decode (json_encode ($travel), FALSE);

        $travelModel = new Travel();
        $travelModel->action = Travel::MISSION_SPIONAGE;
        $travelModel->from = $travel->from;
        $travelModel->to = $travel->to;

        $travelModel =  $this->start($player,$travelModel);

        $spyModel = new Espionage();
        $spyModel->spy = $player;
        $spyModel->travel = $travelModel->id;
        $spyModel->typeSpy = $travel->type;
        $spyModel->planet = $travel->to;

        if($spyModel->typeSpy == Espionage::TYPE_SPY_TROOP){
            $units = Unit::select('id','name')->get();
            for($i = 0;$i < count($units);$i++)
            {
                 $units[$i]['quantity'] = 0;
            }
            $spyModel->troop =  json_encode($units);
        }

        if($spyModel->typeSpy == Espionage::TYPE_SPY_FLEET){
            $ships = Ship::select('id','name')->get();
            for($i = 0;$i < count($ships);$i++)
            {
                 $ships[$i]['quantity'] = 0;
            }
            $spyModel->fleet =  json_encode($ships);
        }

        $spyModel->save();
    }
}

