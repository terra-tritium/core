<?php

namespace App\Services;

use App\Services\ProductionService;
use App\Models\Shipyard;
use App\Models\Unit;

class ShipyardService
{
    private $productionService;

    public function __construct() {
        $this->productionService = new ProductionService();
    }

    public function production ($player, $planet, $unit) {
        if ($this->productionService->hasFunds($unit, $planet)) {
            $this->productionService->add($player, $planet, $unit, "troop");
            $this->productionService->spendFunds($planet, $unit);
        } else {
            return "No suficients Funds";
        }
    }

    public function productionShipyard($player,$planet){
        return $this->productionService->productionPlayer($player,$planet);
    }

    public function shipyards($player,$planet){
        $shipyard = Shipyard::with('unitsShipyard')->where(['player' => $player, 'planet' => $planet])->get();
        return $shipyard;
    }
    
    public function getShipyardPlayer($player){
        $shipyard= new Shipyard();
        $shipyards = $shipyard->getTroopPlayer($player);
        return $shipyards;
    }
}