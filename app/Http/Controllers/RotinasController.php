<?php

namespace App\Http\Controllers;

use App\Models\Trading;
use App\Services\TradingService;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RotinasController extends Controller
{
  public function exec()
  {
    try{
      $alianceController = new AliancesController();
      $tradeService = new TradingService(new Trading());
      $tradeService->verificaAndamentoSafe();
      $alianceController->getScoresAliance();
      $this->updateRanking();
      return response()->json(['message' => 'executado'], Response::HTTP_OK);
    }catch(Exception $e){
      return response()->json(['message'=>"Erro ao executar rotinas", 'msg'=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
   
  }
  /**
   * Execute the console command.
   *
   * @return int
   */
  public function updateRanking()
  {
    try {
      DB::table('ranking')->truncate();

      $players = DB::table('players')
        ->select('id', 'name', 'score', 'buildScore', 'attackScore', 'defenseScore', 'militaryScore', 'researchScore', 'aliance')
        ->orderBy('score', 'desc')
        ->get();

      $count = 0;

      foreach ($players as $player) {
        $count++;
        DB::table('ranking')->insert([
          'name' => $player->name,
          'position' => $count,
          'player' => $player->id,
          'energy' => $this->calculateTotalEnergy($player->id),
          'score' => $player->score,
          'buildScore' => $player->buildScore,
          'attackScore' => $player->attackScore,
          'defenseScore' => $player->defenseScore,
          'militaryScore' => $player->militaryScore,
          'researchScore' => $player->researchScore,
          'aliance' => $player->aliance,
        ]);
      }
    } catch (\Exception $exception) {
      Log::error('Erro no agendamento: ' . $exception->getMessage());
      //    Notification::route('discord', 'terra-tritium')->notify(new ExceptionNotification($exception));

    }
  }
  protected function calculateTotalEnergy($playerId)
  {
    $totalEnergy = DB::table('planets')
      ->where('player', $playerId)
      ->sum('energy');

    return $totalEnergy;
  }
  
}
