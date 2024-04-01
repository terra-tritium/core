<?php

namespace App\Services;

use App\Models\Building;
use App\Models\Planet;
use App\Models\Player;
use App\Services\WorkerService;

class RobotFactoryService
{

  private $workerService;
  public function __construct(WorkerService $workerService)
  {
    $this->workerService = $workerService;
  }

  /**
   * Create a new humanoid
   *
   * @param int $planetId
   * @param int $qtd
   * @return void
   */
  public function createHumanoid($planetId, $qtd) {
    $user = auth()->user()->id;
    $player = Player::where("user", $user)->firstOrFail();
    $planet = Planet::where("id", $planetId)->where("player", $player->id)->firstOrFail();
    $humanoidFactoryBuilding = Building::where('planet', $planetId)
                                        ->where('build', 3) 
                                        ->get();                                        
    
    if (count($humanoidFactoryBuilding) == 0 || $qtd > $humanoidFactoryBuilding[0]->max_humanoids) {
        return false; 
    }

    $this->workerService->syncronizeEnergy($planet);

    $energyCost = $qtd * config("app.tritium_humanoid_price");
    $metalCost = $qtd * config("app.tritium_humanoid_price");
    
    # enough energy and metal?
    if ($planet->energy < $energyCost || $planet->metal < $metalCost) {
      return false;
    }

    $player->score += $qtd * (config("app.tritium_humanoid_price") / 10);

    $planet->energy -= $energyCost;
    $planet->metal -= $metalCost;
    $planet->workers += $qtd;
    $planet->workersWaiting += $qtd;

    $player->save();
    $planet->save();
  }
}