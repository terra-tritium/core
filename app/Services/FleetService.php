<?php

namespace App\Services;

use App\Services\ProductionService;

class FleetService
{
    private $productionService;

    public function __construct() {
        $this->productionService = new ProductionService();
    }

    public function production ($player, $planet, $unit) {
        if ($this->productionService->hasFunds($unit, $player)) {
            $this->productionService->add($player, $planet, $unit, "fleet");
            $this->productionService->spendFunds($player, $unit);
        } else {
            return "No suficients Funds";
        }
    }
}