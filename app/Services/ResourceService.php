<?php

namespace App\Services;

use App\Models\Logbook;
use App\Models\Planet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Jobs\ResourceJob;
use App\Models\Player;
use App\Models\ProcessJob;
use Carbon\Carbon;

class ResourceService
{

    public function __construct(protected readonly TravelService $travelService)
    { }

    public function sendResources(Request $request)
    {
        $planetOrigin = Planet::findOrFail($request->input('origin'));
        $planetTarget = Planet::findOrFail($request->input('target'));
        $playerOrigin = Player::findOrFail($planetOrigin->player);

        if (!$planetTarget->player) return response()->json(['error' => 'Unexplored planet'], Response::HTTP_BAD_REQUEST);
        if ($playerOrigin->transportShips === 0) return response()->json(["error" => "You do not have a sufficient quantity of cargo ships"], Response::HTTP_BAD_REQUEST);

        $metal = $request->input("metal");
        $uranium = $request->input("uranium");
        $crystal = $request->input("crystal");

        if ($planetOrigin->metal < $metal) return response()->json(["error" => "You do not have sufficient resources to send"], Response::HTTP_BAD_REQUEST);
        if ($planetOrigin->uranium < $uranium) return response()->json(["error" => "You do not have sufficient resources to send"], Response::HTTP_BAD_REQUEST);
        if ($planetOrigin->crystal < $crystal) return response()->json(["error" => "You do not have sufficient resources to send"], Response::HTTP_BAD_REQUEST);

        $capacityTransportShips =  $playerOrigin->transportShips * config("app.tritium_transportship_capacity");
        $totalRecursos  = $metal + $uranium + $crystal ;

        #Verificar se tem cargueiro disponível
        if ($totalRecursos > $capacityTransportShips) return response()->json(["error" => "Your freighter does not have the capacity to send all the resources"], Response::HTTP_BAD_REQUEST);

        $transportShipsInUse = floor($totalRecursos / config("app.tritium_transportship_capacity"));

        $transportShipsInUse += ($totalRecursos % config("app.tritium_transportship_capacity")) > 0 ? 1 : 0;

        $planetOrigin->metal -= $metal;
        $planetOrigin->uranium -= $uranium;
        $planetOrigin->crystal -= $crystal;
        $playerOrigin->transportShips -= $transportShipsInUse;

        $planetOrigin->save();
        $playerOrigin->save();


        $timeLoad = $transportShipsInUse * config("app.tritium_charging_speed");

        #Salva o job para acompanhamento até a execução
        $processJob = new ProcessJob();
        $processJob->player = $planetOrigin->player;
        $processJob->planet = $planetOrigin->id;
        $processJob->finished =  Carbon::now()->addSeconds($timeLoad)->getTimestamp();
        $processJob->type = ProcessJob::TYPE_CARRYING;
        $processJob->save();

        #Job carregamento recursos
        ResourceJob::dispatch(
            $this->travelService,
            $planetOrigin->player,
            $planetOrigin->id,
            $planetTarget->id,
            $metal,
            $uranium,
            $crystal,
            $transportShipsInUse
        )->delay(now()->addSeconds($timeLoad));

        $log = new Logbook();
        $log->player = $planetOrigin->player;
        $log->text = "Resource loading process started, expected to be ready " . now()->addSeconds($timeLoad);
        $log->type = "Transport";
        $log->save();

        return response()->json(["message" => "atualizados"], Response::HTTP_OK);

    }

}
