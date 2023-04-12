<?php

namespace App\Services;

use App\Services\ProductionService;

class TroopService
{
    private $productionService;

    public function __construct() {
        $this->productionService = new ProductionService();
    }

    public function production ($player, $planet, $units) {
        if ($this->productionService->hasFunds($units, $player)) {
            $this->productionService->add($player, $planet, $units, "troop");
            $this->productionService->spendFunds($player, $units);
        } else {
            return "No suficients Funds";
        }
    }
}