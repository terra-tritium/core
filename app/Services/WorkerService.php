<?php

namespace App\Services;

use App\Models\Planet;
use App\Models\Building;
use App\Models\Build;
use App\Services\PlanetService;

class WorkerService
{
  private $planetService;
      
  public function __construct() {
    $this->planetService = new PlanetService();
  }

  public function configWorkers ($planetId, $workers, $buildingId) {
    $planet = Planet::find($planetId);
    $building = Building::find($buildingId);

    switch ($building->build) {
      // Metal
      case Build::METALMINING :
          $planet->workersOnMetal = 0;
          break;
      // Uranium
      case Build::URANIUMMINING : 
          $planet->workersOnUranium = 0;
          break;
      // Crystal
      case Build::CRYSTALMINING : 
          $planet->workersOnCrystal = 0;
          break;
      // Laboratory
      case Build::LABORATORY : 
        $planet->workersOnLaboratory = 0;
        break;
    }

    if ($this->waitingWorkers($planet) < $workers) {
      return "Insuficients waiting workers";
    }
    
    if ($workers < 1) {
      return "Invalid workers";
    } else {    
        switch ($building->build) {
            // Metal
            case Build::METALMINING :
                $planet->metal = $this->planetService->currentBalance($planet, 1);
                $planet->timeMetal = time();
                $planet->pwMetal = $workers;
                $planet->workersOnMetal = $workers;
                $planet->workersWaiting = $this->waitingWorkers($planet);
                $this->syncronizeEnergy($planet);
                break;

            // Uranium
            case Build::URANIUMMINING : 
                $planet->uranium = $this->planetService->currentBalance($planet, 2);
                $planet->timeUranium = time();
                $planet->pwUranium = $workers;
                $planet->workersOnUranium = $workers;
                $planet->workersWaiting = $this->waitingWorkers($planet);
                $this->syncronizeEnergy($planet);
                break;

            // Crystal
            case Build::CRYSTALMINING : 
                $planet->crystal = $this->planetService->currentBalance($planet, 3);
                $planet->timeCrystal = time();
                $planet->pwCrystal = $workers;
                $planet->workersOnCrystal = $workers;
                $planet->workersWaiting = $this->waitingWorkers($planet);
                $this->syncronizeEnergy($planet);
                break;

            // Laboratory
            case Build::LABORATORY : 
              $planet->researchPoints = $this->planetService->currentBalance($planet, 4);
              $planet->timeResearch = time();
              $planet->pwResearch = $workers;
              $planet->workersOnLaboratory = $workers;
              $planet->workersWaiting = $this->waitingWorkers($planet);
              $this->syncronizeEnergy($planet);
              break;
        }

        $building->workers = $workers;

        $building->save(); 
        $planet->save();
    }
  }

  private function waitingWorkers(Planet $planet) {
    return $planet->workers - ($planet->workersOnMetal + $planet->workersOnUranium + $planet->workersOnCrystal + $planet->workersOnLaboratory);
  }

  public function syncronizeEnergy(Planet $planet) {

    if ($planet->timeEnergy == null) { return false; }
    if ($planet->timeEnergy == 0) { return false; }
     
    try { 
      $level = Building::where(['build' => Build::ENERGYCOLLECTOR, 'planet' => $planet->id])->first()->level;

      $fator = 0;
      $padronizedLevel = $level * 10;
      if ($padronizedLevel < $planet->workersWaiting) {
        $fator = $padronizedLevel;
      } else {
        $fator = $planet->workersWaiting;
      }
      $planet->energy += ((time() - $planet->timeEnergy) / 360) * env('TRITIUM_ENERGY') * $fator;
      $planet->timeEnergy = time();
      $planet->save();
    } catch (\Exception $exception) {
      return false;
    }
  }
}