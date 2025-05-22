<?php

namespace App\Services;

use App\Models\Fleet;
use App\Services\ProductionService;
use App\Models\Fighters;
use App\Models\Travel;
use App\Services\PlanetService;
use App\Jobs\TravelJob;
use App\Models\Planet;
use App\Models\Logbook;

class FleetService
{
    private $productionService;
    protected $planetService;

    public function __construct() {
        $this->productionService = new ProductionService();
        $this->planetService = new PlanetService();
    }

    public function production ($player, $planet, $ship) {
        if ($this->productionService->hasFunds($ship, $planet, "fleet") && $this->productionService->hasHumanoids($ship, $planet)) {
            $this->productionService->add($player, $planet, $ship, "fleet");
            $this->productionService->spendFunds($planet, $ship, "fleet");
            $this->productionService->spendHumanoids($planet, $ship);
        } else {
            return "No suficients Funds or Humanoids";
        }
    }
    
    public function getFleetPlayer($player){
        $fleet = new Fleet();
        $fleets = $fleet->getFleetPlayer($player);
        return $fleets;
    }

    public function productionFleet($player,$planet){
        return $this->productionService->productionPlayer($player,$planet,'fleet');
    }

    public function fleets($player,$planet){
        $fleets = Fleet::with('ship')->where(['player' => $player, 'planet' => $planet])->get();
        return $fleets;
    }

    public function listExternalFleets($player, $planet) {
        return Fighters::where(['player' => $player, 'combat' => 0, 'planet' => $planet])->first();
    }

    # Tras as naves de volta do planeta onde estavam defendendo
    public function return($player, $planet) {
        $fighter = Fighters::where(['player' => $player, 'combat' => 0, 'planet' => $planet])->first();
        $planetDefense = Planet::find($planet);

        $planetOrigem = Planet::where(['player' => $player])->first();

        $now = time();
        $travel = new Travel();
        $travel->from = $fighter->planet;
        $travel->to = $planetOrigem->id;
        $travel->action = Travel::DEFENSE_RETURN;
        $travel->player = $player;
        $travel->receptor = $player;
        $travel->start = time();
        $travelTime = $this->planetService->calculeDistance($fighter->planet, $planetOrigem->id);
        $travel->arrival = time() + $travelTime;
        $travel->status = Travel::STATUS_ON_GOING;
        $travel->metal = 0;
        $travel->crystal = 0;
        $travel->uranium = 0;
        $travel->status = Travel::STATUS_ON_GOING;
        $travel->start = $now;
        
        $travel->cruiser = $fighter->cruiser;
        $travel->craft = $fighter->craft;
        $travel->bomber = $fighter->bomber;
        $travel->scout = $fighter->scout;
        $travel->stealth = $fighter->stealth;
        $travel->flagship = $fighter->flagship;
        $travel->save();

        $log = new Logbook();
        $log->player = $player;
        $log->text = "Fleet start return travel from ".$planetDefense->name." to " . $planetOrigem->name;
        $log->type = "Travel";
        $log->save();

        TravelJob::dispatch($this, $travel->id, false)->delay(now()->addSeconds($travelTime));

        # remove as naves do planeta que estava sendo defendido
        $fighter->delete();

        return $travel;
    }
}
