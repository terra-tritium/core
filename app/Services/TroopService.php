<?php

namespace App\Services;

use App\Services\ProductionService;
use App\Models\Troop;
use App\Models\Unit;

class TroopService
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

    public function productionTroop($player,$planet){
        return $this->productionService->productionPlayer($player,$planet);
    }

    public function troops($player,$planet){
        $troops = Troop::where(['player' => $player, 'planet' => $planet])->get();
        return $troops;
    }
    
}