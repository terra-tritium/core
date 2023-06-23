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
        if ($this->productionService->hasFunds($unit, $planet)) {
            $this->productionService->add($player, $planet, $unit, "fleet");
            $this->productionService->spendFunds($planet, $unit);
        } else {
            return "No suficients Funds";
        }
    }
}
