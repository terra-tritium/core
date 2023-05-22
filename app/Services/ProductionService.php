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

  public function add ($player, $planet, $units, $type) {
    $production = new Production();
    $production->player = $player;
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
        $player,
        $newUnits,
        $production->id
      )->delay(now()->addSeconds($finalTime * ( env("TRITIUM_PRODUCTION_SPEED") / 1000 ) ));
    }

    if ($type == "fleet") {
      FleetJob::dispatch(
        $planet,
        $player,
        $newUnits,
        $production->id
      )->delay(now()->addSeconds($finalTime * ( env("TRITIUM_PRODUCTION_SPEED") / 1000 ) ));
    }
  }

  public function hasFunds($units, $player) {
    $p1 = Player::findOrFail($player);
    foreach ($units as $key => $unit) {
        if (array_key_exists("quantity", $unit)) {
            $unitModel = Unit::find($unit["id"]);
            if (!$this->playerService->enoughBalance($p1, ($unitModel->metal * $unit["quantity"]), 1)){
                return false;
            }
            if (!$this->playerService->enoughBalance($p1, ($unitModel->uranium * $unit["quantity"]), 2)){
                return false;
            }
            if (!$this->playerService->enoughBalance($p1, ($unitModel->crystal * $unit["quantity"]), 3)){
                return false;
            }
        }
    }
    return true;
  }

  public function spendFunds($player, $units) {
    $metal = 0;
    $uranium = 0;
    $crystal = 0;

    foreach ($units as $key => $unit) {
        $unitModel = Unit::find($unit["id"]);
        $metal += $unitModel->metal;
        $uranium += $unitModel->uranium;
        $crystal += $unitModel->crystal;
    }

    $p1 = Player::findOrFail($player);
    $p1 = $this->playerService->removeMetal($p1, $metal);
    $p1 = $this->playerService->removeUranium($p1, $uranium);
    $p1 = $this->playerService->removeCrystal($p1, $crystal);
    $p1->save();
}
}