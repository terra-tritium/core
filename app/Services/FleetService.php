<?php

namespace App\Services;

use App\Models\Fleet;
use App\Services\ProductionService;

class FleetService
{
    private $productionService;

    public function __construct() {
        $this->productionService = new ProductionService();
    }

    public function production ($player, $planet, $ship) {
        if ($this->productionService->hasFunds($ship, $planet)) {
            $this->productionService->add($player, $planet, $ship, "fleet");
            $this->productionService->spendFunds($planet, $ship);
        } else {
            return "No suficients Funds";
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
}
