<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Trading;
use App\Services\AlianceService;
use App\Services\TradingService;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RotinasController extends Controller
{
  public function exec()
  {
    try {
      $alianceController = new AliancesController(new AlianceService());
      $tradeService = new TradingService(new Trading(), new LogbookController());
      $responseTrade = $tradeService->verificaAndamentoSafe();
      $responseAliance = $alianceController->getScoresAliance();
      $responseRanking = $this->updateRanking();
      return response()->json([
        "trade" => $responseTrade,
        "aliance" => $responseAliance,
        "raking" => $responseRanking
      ], Response::HTTP_OK);
    } catch (Exception $e) {
      return response()->json(['message' => "Erro ao executar rotinas", 'msg' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
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

      $players = DB::table('players as p')
        ->select('p.id', 'p.name', 'p.score', 'p.buildScore', 'p.attackScore', 'p.defenseScore',
         'p.militaryScore', 'p.researchScore', 'p.aliance','a.name as alianceName')
        ->leftJoin('aliances as a','a.id','=','p.aliance')
        ->orderBy('score', 'desc')
        ->get();

      $count = 0;

      //aki
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
          'alianceName' => $player->alianceName
        ]);
      }
      return "executado";
    } catch (\Exception $exception) {
      Log::error('Erro no agendamento: ' . $exception->getMessage());
      return "Não executado ".$exception->getMessage();
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
