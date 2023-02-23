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
    foreach ($units as $key => $unit) {
        $finalTime += $unit["time"];
    }
    $production->ready = time() + $finalTime;
    $production->objects = json_encode($units);
    $production->save();

    TroopJob::dispatch($planet, $units)->delay(now()->addSecounds($finalTime * env("TRITIUM_PRODUCTION_SPEED")));
  }
}