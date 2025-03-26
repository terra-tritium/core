<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use app\Models\Reward;
use App\Models\Player;
use App\Models\Planet;

class RewardService
{
  public function claim($code, $wallet, $planetId)
  {
    try {
      $player = Player::getPlayerLogged();
      $existsReward = Reward::where([['user', $player->user->id], ['code', $code]])->first();
      $existsPlanet = Planet::find($planetId);

      if (!$this->isValidCode($code)) {
        return "Invalid claim code";
      }

      if (!$existsPlanet) {
        return "Invalid Planet";
      }

      if ($existsReward) {
        return "Player no elegible for claim";
      }

      $this->processReward($code, $player->id, $planetId);

      $reward = new Reward();
      $reward->code = $code;
      $reward->user = $player->user->id;
      $reward->used = 1;
      $reward->wallet = $wallet;
      $reward->save();

      return "ok";

    } catch (Exception $e) {
      Log::error('Erro ao recuperar mensagens em grupo: ' . $e->getMessage());
      return response()->json(
          ['message' => "Erro ao enviar messagem", 'error' => $e->getMessage()],
          Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }

  private function isValidCode($code) {
    switch ($code) {
      case "A001" : return true;
      case "A002" : return true;
      default: return false;
    }
  }

  private function processReward($code, $playerId, $planetId) {
    switch ($code) {
      case "A001" : $this->addMetal($planetId, 100000);
        break;
      case "A002" : $this->addResearchPoints($playerId, 10000);
        break;
    }
  }

  private function addMetal($playerId, $qtd) {
    $planet = Planet::find($playerId);
    $planet->metal += $qtd;
    $planet->save();
  }

  private function addResearchPoints($playerId, $qtd) {
    $player = Player::find($playerId);
    $player->researchPoints += $qtd;
    $player->save();
  }

}
