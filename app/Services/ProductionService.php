<?php

namespace App\Services;

use App\Jobs\ShipyardJob;
use App\Models\Production;
use App\Models\Player;
use App\Models\Unit;
use App\Jobs\TroopJob;
use App\Jobs\FleetJob;
use App\Models\Planet;
use App\Models\UnitShipyard;
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
  
      $timeConstruction = ($unitModel->time *  $unit['quantity'] * env("TRITIUM_PRODUCTION_SPEED") );
      $newUnit[] = $unit;
      
      $production->ready = time() + $timeConstruction;
      $production->objects = json_encode($newUnit);
      $production->executed = false;
      $production->save();
  
      if ($type == "troop") {
        TroopJob::dispatch(
          $planet,
          $player,
          $newUnit,
          $production->id
        )->delay(now()->addSeconds($timeConstruction));
      }
  
      if ($type == "fleet") {
        FleetJob::dispatch(
          $planet,
          $player,
          $newUnit,
          $production->id
        )->delay(now()->addSeconds($timeConstruction));
      }

    }else{
      return false;
    }
    
  }
  public function addShipyard ($player, $planet, $unitShipyard, $type) {
    $unitModel = UnitShipyard::findOrFail($unitShipyard['id']);
    if($unitModel){
      $production = new Production();
      $production->player = $player;
      $production->planet = $planet;
  
      $timeConstruction = ($unitModel->time *  $unitShipyard['quantity'] * env("TRITIUM_PRODUCTION_SPEED") );
      $newUnit[] = $unitShipyard;
      
      $production->ready = time() + $timeConstruction;
      $production->objects = json_encode($newUnit);
      $production->executed = false;
      $production->save();
  
      if ($type == "shipyard") {
        ShipyardJob::dispatch(
          $planet,
          $player,
          $newUnit,
          $production->id
        )->delay(now()->addSeconds($timeConstruction));
      }

    }else{
      return false;
    }
    
  }  

  public function hasFunds($unit, $unitShipyard, $planet) {
    $unitModel = Unit::findOrFail($unit["id"]);
    $unitModelShipyard = UnitShipyard::findOrFail($unitShipyard["id"]);

    if (isset($unit["quantity"])) {
      $p1 = Planet::findOrFail($planet);
      if (!$this->planetService->enoughBalance($p1, ($unitModel->metal * $unit["quantity"]), 1)){
            return false;
        }
        if (!$this->planetService->enoughBalance($p1, ($unitModel->uranium * $unit["quantity"]), 2)){
            return false;
        }
        if (!$this->planetService->enoughBalance($p1, ($unitModel->crystal * $unit["quantity"]), 3)){
            return false;
        }
    } else if (isset($unitShipyard["quantity"])) {
      $p1 = Planet::findOrFail($planet);
      if (!$this->planetService->enoughBalance($p1, ($unitModelShipyard->metal * $unitShipyard["quantity"]), 1)){
            return false;
        }
        if (!$this->planetService->enoughBalance($p1, ($unitModelShipyard->uranium * $unitShipyard["quantity"]), 2)){
            return false;
        }
        if (!$this->planetService->enoughBalance($p1, ($unitModelShipyard->crystal * $unitShipyard["quantity"]), 3)){
            return false;
        }
    }
    return true;
  }

  public function spendFunds($planet, $unit, $unitShipyard) {
    $metal = 0;
    $uranium = 0;
    $crystal = 0;

    $unitModel = Unit::findOrFail($unit["id"]);
    $metal += $unitModel->metal;
    $uranium += $unitModel->uranium;
    $crystal += $unitModel->crystal;

    $unitModelShipyard = UnitShipyard::findOrFail($unitShipyard["id"]);
    $metal += $unitModelShipyard->metal;
    $uranium += $unitModelShipyard->uranium;
    $crystal += $unitModelShipyard->crystal;

    $p1 = Planet::findOrFail($planet);
    $p1 = $this->planetService->removeMetal($p1, $metal);
    $p1 = $this->planetService->removeUranium($p1, $uranium);
    $p1 = $this->planetService->removeCrystal($p1, $crystal);
    $p1->save();
  }

  public function productionPlayer($player,$planet,$executed = false){
    
    $filter = ['player'=> $player->id,'executed'=> $executed];
    if(!is_null($planet) && !empty($planet))
    {
      $filter['planet'] = $planet;
    }
    $productionModel =  Production::where($filter)->orderBy("ready")->get();
    $units = [];
    $unitShipyard = [];

    foreach($productionModel as $key => $production){
      $unitObj = json_decode($production->objects);
      
      foreach($unitObj as $key => $unit){
        $unitModel = Unit::find($unit->id);

        $unitModel->ready =  $production->ready;
        $unitModel->planet =  $production->planet;
        $unitModel->quantity =  $unit->quantity;

        $units[] =  $unitModel ;
      }
      foreach($unitObj as $key => $unitShipyard){
        $unitModelShipyard = UnitShipyard::find($unit->id);

        $unitModelShipyard->ready =  $production->ready;
        $unitModelShipyard->planet =  $production->planet;
        $unitModelShipyard->quantity =  $unit->quantity;

        $unitShipyard[] =  $unitModelShipyard ;
      }
    }
    return  $units;
  }
  

}