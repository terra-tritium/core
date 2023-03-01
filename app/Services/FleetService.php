<?php

namespace App\Services;

use App\Services\ProductionService;

class FleetService
{
    private $productionService;

    public function __construct() {
        $this->productionService = new ProductionService();
    }

    public function production ($address, $planet, $units) {
        if ($this->productionService->hasFunds($units, $address)) {
            $this->productionService->add($address, $planet, $units, "fleet");
            $this->productionService->spendFunds($address, $units);
        } else {
            return "No suficients Funds";
        }
    }
}