<?php

namespace App\Services;

use App\Models\Fleet;
use App\Models\Planet;
use App\Models\Ship;

class DominationService
{

  public function dominatePlanet($travel) {
    $planet = Planet::find($travel->to);
    if (!$planet) { return false; }
    $this->dominate($travel->to, $travel->player);
    $this->landingShips($travel);
  }

  public function dominate($planetId, $playerId) {
    $planet = Planet::find($planetId);

    if (!$planet) { return false; }
    if ($planet->player != null) { return false; }

    $planet->dominator = $playerId;
    $planet->onFire = 0;
    $planet->save();

    return true;
  }

  public function leaveDomination($travel) {
    $planet = Planet::find($travel->from);
    if (!$planet) { return false; }
    $planet->dominator = 0;
    $planet->onFire = 0;
    $planet->save();
    $this->landingShips($travel);
    $this->removeShips($travel);
    return true;
  }

  public function removeShips($travel) {
    $this->removeShip($travel->player, $travel->from, Ship::SHIP_CRAFT,    );
    $this->removeShip($travel->player, $travel->from, Ship::SHIP_BOMBER,   );
    $this->removeShip($travel->player, $travel->from, Ship::SHIP_CRUISER,  );
    $this->removeShip($travel->player, $travel->from, Ship::SHIP_SCOUT,    );
    $this->removeShip($travel->player, $travel->from, Ship::SHIP_STEALTH,  );
    $this->removeShip($travel->player, $travel->from, Ship::SHIP_FLAGSHIP, );
  }

  private function removeShip($player, $planet, $ship) {
    $existentFleet = Fleet::where([['player', $player], ['planet', $planet], ['unit', $ship]])->first();
    if ($existentFleet) {
      $existentFleet->quantity = 0;
      $existentFleet->save();
    }
  }

  public function landingShips($travel) {
    $this->landingShip($travel->player, $travel->to, Ship::SHIP_CRAFT,    $travel->craft);
    $this->landingShip($travel->player, $travel->to, Ship::SHIP_BOMBER,   $travel->bomber);
    $this->landingShip($travel->player, $travel->to, Ship::SHIP_CRUISER,  $travel->cruiser);
    $this->landingShip($travel->player, $travel->to, Ship::SHIP_SCOUT,    $travel->scout);
    $this->landingShip($travel->player, $travel->to, Ship::SHIP_STEALTH,  $travel->stealth);
    $this->landingShip($travel->player, $travel->to, Ship::SHIP_FLAGSHIP, $travel->flagship);
  }

  private function landingShip($player, $planet, $ship, $quantity) {
    $existentFleet = Fleet::where([['player', $player], ['planet', $planet], ['unit', $ship]])->first();

    if ($existentFleet) {
      $fleet = $existentFleet;
      $fleet->quantity += $quantity;
    } else {
      $fleet = new Fleet();
      $fleet->quantity = $quantity;
    }
    
    $fleet->player = $player;
    $fleet->planet = $planet;
    $fleet->unit = $ship;
    $fleet->save();
  }

}