<?php

namespace App\Services;

use App\Models\Building;
use App\Models\Planet;
use App\Models\Player;
use App\Services\WorkerService;

class RobotFactoryService
{

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
    $humanoidFactoryBuilding = Building::where('planet_id', $planetId)
                                        ->where('type', 3) 
                                        ->first();
                                        

    if (!$humanoidFactoryBuilding || $qtd > $humanoidFactoryBuilding->max_humanoids) {
        return false; 
    }

    $this->workerService->syncronizeEnergy($planet);

    $energyCost = $qtd * env('TRITIUM_HUMANOID_PRICE');
    $metalCost = $qtd * env('TRITIUM_HUMANOID_PRICE');

    # enough energy and metal?
    if ($planet->energy < $energyCost || $planet->metal < $metalCost) {
      return false;
    }

    $player->score += $qtd * env('TRITIUM_HUMANOID_POINTS');

    $planet->energy -= $energyCost;
    $planet->metal -= $metalCost;
    $planet->workers += $qtd;
    $planet->workersWaiting += $qtd;

    $player->save();
    $planet->save();
  }
}