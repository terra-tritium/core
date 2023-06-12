<?php

namespace App\Services;

use App\Models\Building;
use App\Models\Planet;
use App\Models\Player;
use App\Services\PlanetService;

class RobotFactoryService
{

  public function __construct(PlanetService $planetService)
  {
    $this->planetService = $planetService;
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

    $this->planetService->syncronizeEnergy($planet);

    $cost = $qtd * env('TRITIUM_HUMANOID_PRICE');

    # enough energy?
    if ($planet->energy < $cost) {
      return false;
    }

    $planet->energy -= $cost;
    $planet->workers += $qtd;
    $planet->workersWaiting += $qtd;

    $planet->save();
  }
}