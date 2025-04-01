<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Models\Building;
use App\Models\Player;
use App\Models\Planet;
use App\Services\PlanetService;

class TritiumService
{
  public function upgrade($planetId, $building) {
    try {
      $building = Building::find($building);
      $player = Player::getPlayerLogged();
      $planet = Planet::find($planetId);

      $playerDTO = Player::find($player->id);

      if (!$building) {
        return "Building not found";
      }

      if (!$this->hasFunds($player, $planet)) {
        return "No suficient funds";
      }

      if ($building->level == 1) {
        $building->start_tritium = time();
        $building->tritium = 0;
      } else {
        $building->tritium += (time() - $building->start_tritium) * 0.0001;
      }

      $this->spendResources($playerDTO, $planet);

      $building->level += 1;
      $building->save();
      
      return "ok";

    } catch (Exception $e) {
      Log::error('Erro ao executar tritium upgrade: ' . $e->getMessage());
      return response()->json(
          ['message' => "Erro ao executar tritium upgrade", 'error' => $e->getMessage()],
          Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }

  public function claim($building) {
    try {
      $player = Player::getPlayerLogged();
      $playerDTO = Player::find($player->id);
      $building = Building::find($building);
      $building->tritium += (time() - $building->start_tritium) * 0.0001;
      $playerDTO->tritium += $building->tritium;
      $playerDTO->save();

      return "ok";

    } catch (Exception $e) {
      Log::error('Erro ao executar tritium claim: ' . $e->getMessage());
      return response()->json(
          ['message' => "Erro ao executar tritium claim", 'error' => $e->getMessage()],
          Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }

  private function hasFunds($player, $planet) {
    $planetService = new PlanetService();

    if ($player->tritium < 5000) {
      return false;
    }

    if (!$planetService->enoughBalance($planet, 9000, 3)) {
      return false;
    }

    return true;
  }

  private function spendResources($player, $planet) {
    $planetService = new PlanetService();
    $player->tritium -= 5000;
    $planet = $planetService->removeCrystal($planet, 9000);
    $planet->save();
  }
}
