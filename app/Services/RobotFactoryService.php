<?php

namespace App\Services;

use App\Models\Building;
use App\Models\Planet;
use App\Models\Player;

class RobotFactoryService
{
  public function setEnergy($planetId, $qtd) {

    $user = auth()->user()->id;
    $player = Player::where("user", $user)->firstOrFail();

    $planet = Planet::where("id", $planetId)->where("player", $player->id)->firstOrFail();
    $robotFactoryBuild = Building::where("planet", $planet->id)->where("build", 3)->firstOrFail();

    $maxEnergy = $robotFactoryBuild->level * 100;
    if ($qtd > $maxEnergy) {
      return 0;
    }

    $planet = $this->sincronizeEnergyUse($planet);

    $planet->useEnergyByFactory = $qtd;
    $planet->timeEnergyByFactory = time();
    $planet->pwWorker = $qtd / 100;
    $planet->save();
    return $planet->useEnergyByFactory;
  }

  public function sincronizeEnergyUse(Planet $planet) {

    if ($planet->timeEnergyByFactory == null) {
      return $planet;
    }
    $now = time();
    $time = $planet->timeEnergyByFactory;
    $diff = $now - $time;
    // convert to hours
    $diff = $diff / 3600;
    $energyUsed = $diff * $planet->useEnergyByFactory;
    $planet->energy -= $energyUsed;
    if ($planet->energy < 0) {
      $planet->energy = 0;
    }
    return $planet;
  }
}