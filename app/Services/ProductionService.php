<?php

namespace App\Services;

use App\Jobs\ShipyardJob;
use App\Models\Production;
use App\Models\Player;
use App\Models\Unit;
use App\Jobs\TroopJob;
use App\Jobs\FleetJob;
use App\Models\Planet;
use App\Models\Ship;
use App\Services\PlanetService;

class ProductionService
{

  private $planetService;

  public function __construct() {
    $this->planetService = new PlanetService();
  }

  public function add ($player, $planet, $unit, $type) {
    if($type == "troop"){
      $unitModel = Unit::findOrFail($unit['id']);
    }else{
      $unitModel = Ship::findOrFail($unit['id']);
    }
    if($unitModel){
      $production = new Production();
      $production->player = $player;
      $production->planet = $planet;
  
      $timeConstruction = ($unitModel->time *  $unit['quantity'] * env("TRITIUM_PRODUCTION_SPEED") );
      $unit['type'] = $type;
      
      $production->ready = time() + $timeConstruction;
      // $production->ready = time() + 1;
      $production->objects = json_encode($unit);
      $production->executed = false;
      $production->save();
      if ($type == "troop") {
        TroopJob::dispatch(
          $planet,
          $player,
          $unit,
          $production->id
        )->delay(now()->addSeconds($timeConstruction));
      }
  
      if ($type == "fleet") {
        FleetJob::dispatch(
          $planet,
          $player,
          $unit,
          $production->id
        )->delay(now()->addSeconds($timeConstruction));
      }

    }else{
      return false;
    }
    
  }

  public function hasFunds($unit, $planet, $type) {
    if($type == "troop"){
      $unitModel = Unit::findOrFail($unit['id']);
    }else{
      $unitModel = Ship::findOrFail($unit['id']);
    }

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
    } 
    return true;
  }

  public function spendFunds($planet, $unit, $type) {
    $metal = 0;
    $uranium = 0;
    $crystal = 0;
    $quantity = $unit['quantity'];

    if($type == "troop"){
      $unitModel = Unit::findOrFail($unit['id']);
    }else{
      $unitModel = Ship::findOrFail($unit['id']);
    }
    $metal += ($unitModel->metal * $quantity);
    $uranium += ($unitModel->uranium * $quantity);
    $crystal += ($unitModel->crystal * $quantity);


    $p1 = Planet::findOrFail($planet);
    $p1 = $this->planetService->removeMetal($p1, $metal);
    $p1 = $this->planetService->removeUranium($p1, $uranium);
    $p1 = $this->planetService->removeCrystal($p1, $crystal);
    $p1->save();
  }

  public function productionPlayer($player,$planet, $type ,$executed = false){
    
    $filter = ['player'=> $player->id,'executed'=> $executed];
    if(!is_null($planet) && !empty($planet))
    {
      $filter['planet'] = $planet;
    }
    $productionModel =  Production::where($filter)->orderBy("ready")->get();
    $units = [];
    if($productionModel){
      foreach($productionModel as $production){
        $unitObj = json_decode($production->objects);
        if($unitObj->type == $type){
          if($type == 'troop'){
              $unitModel = Unit::find($unitObj->id);
          }else{
              $unitModel = Ship::find($unitObj->id);
          }
          $unitModel['ready'] =  $production->ready;
          $unitModel['planet'] =  $production->planet;
          $unitModel['quantity'] = $unitObj->quantity;
          $units[] = $unitModel;        
        }
      }
    }
    return  $units;

    // foreach($productionModel as $key => $production){
    //   $unitObj = json_decode($production->objects);
    //   return ['obj' => $unitObj->id];
      
    //   foreach($unitObj as $key => $unit){
    //     if(isset($unit->id)){
    //       if($type == 'troop'){
    //         $unitModel = Unit::find($unit->id);
    //       }else{ 
    //         $unitModel = Ship::find($unit->id);
    //       }
  
    //       $unitModel->ready =  $production->ready;
    //       $unitModel->planet =  $production->planet;
    //       $unitModel->quantity =  $unit->quantity;
  
    //       $units[] =  $unitModel ;
    //     }
       
    //   }

    // }
  }
  

}