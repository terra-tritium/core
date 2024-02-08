<?php

namespace App\Services;

use App\Models\Battle;
use App\Models\BattleStage;
use App\Models\Fighters;
use App\Models\Planet;
use App\Models\Ship;
use App\Jobs\BattleJob;
use App\Models\Building;
use App\Models\Fleet;
use App\Models\Travel;
use App\Models\Troop;

class SpaceBattleService
{
  private $frontLineSize;
  private $longRangeSize;
  private $specialSize;
  private $commandSize;
  
  private $currentStage;
  private $attackReserve;
  private $defenseReserve;
  private $travelService;

  public function __construct() {
    $this->frontLineSize = 50;
    $this->longRangeSize = 20;
    $this->specialSize = 20;
    $this->commandSize = 8;
    $this->currentStage = new BattleStage();
  }

  public function createNewBattle ($travel) {

    $battle = new Battle();
    $battle->planet = $travel->to;
    $battle->status = Battle::STATUS_CREATE;
    $battle->start = time();
    $battle->stage = 0;
    $battle->save();
    
    $player1 = new Fighters();
    $player1->battle = $battle->id;
    $player1->player = $travel->player;
    $player1->side = Battle::SIDE_INVASOR;
    $player1->strategy = $travel->strategy;
    $player1->demage = 0;
    $player1->start = time();
    $player1->stage = 0;
    $player1->planet = $travel->from;
    $player1->cruiser = $travel->cruiser;
    $player1->craft = $travel->craft;
    $player1->bomber = $travel->bomber;
    $player1->scout = $travel->scout;
    $player1->stealth = $travel->stealth;
    $player1->flagship = $travel->flagship;
    $player1->save();

    $planet = Planet::find($travel->to);
    
    $player2 = new Fighters();
    $player2->battle = $battle->id;
    $player2->player = $planet->player;
    $player2->side = Battle::SIDE_LOCAL;
    $player2->strategy = $planet->defenseStrategy;
    $player2->demage = 0;
    $player2->start = time();
    $player2->stage = 0;
    $player2->planet = $travel->to;
    $player2->cruiser = $planet->cruiser;

    $fleet = Fleet::where('planet', $travel->to)->first();
    foreach ($fleet->ships as $ship) {
      switch ($ship->unit) {
        case Ship::SHIP_CRAFT:
          $player2->craft = $ship->quantity;
          break;
        case Ship::SHIP_BOMBER:
          $player2->bomber = $ship->quantity;
          break;
        case Ship::SHIP_CRUISER:
          $player2->cruiser = $ship->quantity;
          break;
        case Ship::SHIP_SCOUT:
          $player2->scout = $ship->quantity;
          break;
        case Ship::SHIP_STEALTH:
          $player2->stealth = $ship->quantity;
          break;
        case Ship::SHIP_FLAGSHIP:
          $player2->flagship = $ship->quantity;
          break;
      }
    }

    $player2->save();
  }

  public function startBattle($battleId) {
    $battle = Battle::find($battleId);
    
    if ($battle->status != Battle::STATUS_CREATE) {
      return false;
    }

    $battle->status = Battle::STATUS_RUNNING;
    $battle->stage = 1;
    $battle->save();
  }

  public function excuteStage($battleId) {
    $battle = Battle::find($battleId);

    if ($battle->status == Battle::STATUS_CREATE) {
      $this->startBattle($battleId);
    }

    $invasors = Fighters::where(['battle'=>$battleId, 'side'=>Battle::SIDE_INVASOR])->first();
    $locals = Fighters::where(['battle'=>$battleId, 'side'=>Battle::SIDE_LOCAL])->first();
  }

  private function resolve($invasors, $locals) {
    foreach ($invasors as $invasor) {
      $invasor->demage = 0;
      $invasor->save();
    }
  }
}
