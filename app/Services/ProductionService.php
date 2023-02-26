<?php

namespace App\Services;

use App\Models\Production;
use App\Jobs\TroopJob;

class ProductionService
{
  public function add ($address, $planet, $units) {
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

    TroopJob::dispatch(
      $planet,
      $address,
      $newUnits,
      $production->id
    )->delay(now()->addSeconds($finalTime * ( env("TRITIUM_PRODUCTION_SPEED") / 1000 ) ));
  }
}