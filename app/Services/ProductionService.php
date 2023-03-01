<?php

namespace App\Services;

use App\Models\Production;
use App\Models\Player;
use App\Models\Unit;
use App\Jobs\TroopJob;
use App\Services\PlayerService;

class ProductionService
{

  private $playerService;

  public function __construct() {
    $this->playerService = new PlayerService();
  }

  public function add ($address, $planet, $units, $type) {
    $production = new Production();
    $production->address = $address;
    $production->planet = $planet;
    $finalTime = 0;
    $newUnits = [];
    foreach ($units as $key => $unit) {
      if (array_key_exists("quantity", $unit)) {
        $finalTime += $unit["time"];
        $newUnits[] = $unit;
      }
    }
    $production->ready = time() + $finalTime;
    $production->objects = json_encode($newUnits);
    $production->executed = false;
    $production->save();

    if ($type == "troop") {
      TroopJob::dispatch(
        $planet,
        $address,
        $newUnits,
        $production->id
      )->delay(now()->addSeconds($finalTime * ( env("TRITIUM_PRODUCTION_SPEED") / 1000 ) ));
    }

    if ($type == "fleet") {
      FleetJob::dispatch(
        $planet,
        $address,
        $newUnits,
        $production->id
      )->delay(now()->addSeconds($finalTime * ( env("TRITIUM_PRODUCTION_SPEED") / 1000 ) ));
    }
  }

  public function hasFunds($units, $address) {
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

  public function spendFunds($address, $units) {
    $metal = 0;
    $deuterium = 0;
    $crystal = 0;

    foreach ($units as $key => $unit) {
        $metal += $unit["metal"];
        $deuterium += $unit["deuterium"];
        $crystal += $unit["crystal"];
    }

    $p1 = Player::where("address", $address)->firstOrFail();
    $p1 = $this->playerService->removeMetal($p1, $metal);
    $p1 = $this->playerService->removeDeuterium($p1, $deuterium);
    $p1 = $this->playerService->removeCrystal($p1, $crystal);
    $p1->save();
}
}