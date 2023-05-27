<?php

namespace App\Services;

use App\Models\Production;
use App\Models\Player;
use App\Models\Unit;
use App\Jobs\TroopJob;
use App\Jobs\FleetJob;
use App\Services\PlanetService;

class ProductionService
{

  private $planetService;

  public function __construct() {
    $this->planetService = new PlanetService();
  }

  public function add ($player, $planet, $unit, $type) {
    $unitModel = Unit::findOrFail($unit['id']);
    if($unitModel){
      $production = new Production();
      $production->player = $player;
      $production->planet = $planet;
      $finalTime = 0;
  
      $finalTime = $unitModel->time;
      $newUnit[] = $unit;
      
      $production->ready = time() + $finalTime;
      $production->objects = json_encode($newUnit);
      $production->executed = false;
      $production->save();
  
      if ($type == "troop") {
        TroopJob::dispatch(
          $planet,
          $player,
          $newUnit,
          $production->id
        )->delay(now()->addSeconds($finalTime * ( env("TRITIUM_PRODUCTION_SPEED") / 1000 ) ));
      }
  
      if ($type == "fleet") {
        FleetJob::dispatch(
          $planet,
          $player,
          $newUnit,
          $production->id
        )->delay(now()->addSeconds($finalTime * ( env("TRITIUM_PRODUCTION_SPEED") / 1000 ) ));
      }
    }else{
      return false;
    }
    
  }

  public function hasFunds($unit, $player) {
    $unitModel = Unit::findOrFail($unit["id"]);

    if (isset($unit["quantity"])) {
      $p1 = Player::findOrFail($player);
      if (!$this->planetService->enoughBalance($p1, ($unitModel->metal * $unit["quantity"]), 1)){
            return false;
        }
        if (!$this->planetService->enoughBalance($p1, ($unitModel->uranium * $unit["quantity"]), 2)){
            return false;
        }
        if (!$this->planetService->enoughBalance($p1, ($unitModel->crystal * $unit["quantity"]), 3)){
            return false;
        }
    }
    return true;
  }

  public function spendFunds($player, $unit) {
    $metal = 0;
    $uranium = 0;
    $crystal = 0;

    $unitModel = Unit::findOrFail($unit["id"]);
    $metal += $unitModel->metal;
    $uranium += $unitModel->uranium;
    $crystal += $unitModel->crystal;

    $p1 = Player::findOrFail($player);
    $p1 = $this->planetService->removeMetal($p1, $metal);
    $p1 = $this->planetService->removeUranium($p1, $uranium);
    $p1 = $this->planetService->removeCrystal($p1, $crystal);
    $p1->save();
  }

  public function productionPlayer($player,$planet){
    
    $filter = ['player'=> $player->id,'executed'=> false];
    if(!is_null($planet) && !empty($planet))
    {
      $filter['planet'] = $planet;
    }
    $productionModel =  Production::where($filter)->get();
    $units = [];

    foreach($productionModel as $key => $production){
      $unitObj = json_decode($production->objects);
      
      foreach($unitObj as $key => $unit){
        $unitModel = Unit::find($unit->id);

        $unitModel->ready =  $production->ready;
        $unitModel->planet =  $production->planet;
        $unitModel->quantity =  $unit->quantity;

        $units[] =  $unitModel ;
      }
    }
    return  $units;
  }
}