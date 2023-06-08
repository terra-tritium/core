<?php

namespace App\Services;

use App\Services\ProductionService;
use App\Models\Troop;

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

    
}