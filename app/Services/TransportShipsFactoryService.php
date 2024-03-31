<?php

namespace App\Services;

use App\Models\Building;
use App\Models\Planet;
use App\Models\Player;
use App\Services\WorkerService;

class TransportShipsFactoryService
{

  private $workerService;
  public function __construct(WorkerService $workerService)
  {
    $this->workerService = $workerService;
  }

  /**
   * Create a new transportship
   *
   * @param int $planetId
   * @param int $qtd
   * @return void
   */
  public function createTransportShip($planetId, $qtd) {
    $user = auth()->user()->id;
    $player = Player::where("user", $user)->firstOrFail();
    $planet = Planet::where("id", $planetId)->where("player", $player->id)->firstOrFail();

    $this->workerService->syncronizeEnergy($planet);

    $energyCost = $qtd * config("app.tritium_transportship_price");
    $metalCost = $qtd * config("app.tritium_transportship_price");

    # enough energy and metal?
    if ($planet->energy < $energyCost || $planet->metal < $metalCost) {
      return false;
    }

    $player->score += $qtd * config("app.tritium_transportship_base");

    $planet->energy -= $energyCost;
    $planet->metal -= $metalCost;
    $player->transportShips += $qtd;

    $player->save();
    $planet->save();

    return $qtd;
  }
}
