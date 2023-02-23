<?php

namespace App\Services;

use App\Models\Troop;
use App\Models\Production;
use App\Models\Player;
use App\Models\Unit;

use App\Services\PlayerService;
use App\Services\ProductionService;

class TroopService
{
    private $playerService;
    private $productionService;

    public function __construct() {
        $this->playerService = new PlayerService();
        $this->productionService = new ProductionService();
    }

    public function production ($address, $planet, $units) {
        if ($this->hasFunds($units, $address)) {
            $this->productionService->add($address, $planet, $units);
            $this->spendFunds($address, $units);
        } else {
            return "No suficients Funds";
        }
    }

    private function hasFunds($units, $address) {
        $p1 = Player::where("address", $address)->firstOrFail();
        foreach ($units as $key => $unit) {
            if (array_key_exists("quantity", $unit)) {
                $unitModel = Unit::find($unit["id"]);
                if (!$this->playerService->enoughBalance($p1, ($unitModel->metal * $unit["quantity"]), 1)){
                    return false;
                }
                if (!$this->playerService->enoughBalance($p1, ($unitModel->deuterium * $unit["quantity"]), 2)){
                    return false;
                }
                if (!$this->playerService->enoughBalance($p1, ($unitModel->crystal * $unit["quantity"]), 3)){
                    return false;
                }
            }
        }
        return true;
    }

    private function spendFunds($address, $units) {
        $metal = 0;
        $deuterium = 0;
        $crystal = 0;

        foreach ($units as $key => $unit) {
            $metal += $unit->metal;
            $deuterium += $unit->deuterium;
            $crystal += $unit->crystal;
        }

        $p1 = Player::where("address", $address)->findOrFail();
        $p1 = $this->playerService->removeMetal($p1, $metal);
        $p1 = $this->playerService->removeDeuterium($p1, $deuterium);
        $p1 = $this->playerService->removeCrystal($p1, $crystal);
        $p1->save();
    }
}