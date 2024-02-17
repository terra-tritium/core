<?php

namespace App\Services;

use App\Models\Logbook;
use App\Models\Planet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Jobs\ResourceJob;

class ResourceService
{

    public function __construct(protected readonly TravelService $travelService)
    { }

    public function sendResources(Request $request)
    {
        $planetOrigin = Planet::findOrFail($request->input('origin'));
        $planetTarget = Planet::findOrFail($request->input('target'));
        if (!$planetTarget->player) return response()->json(['error' => 'Unexplored planet'], Response::HTTP_BAD_REQUEST);
        if ($planetOrigin->transportShips === 0) return response()->json(["error" => "You do not have a sufficient quantity of cargo ships"], Response::HTTP_BAD_REQUEST);
        
        $metal = $request->input("metal");
        $uranium = $request->input("uranium");
        $crystal = $request->input("crystal");

        if ($planetOrigin->metal < $metal && $planetOrigin->uranium < $uranium && $planetOrigin->crystal < $crystal) 
        {
            return response()->json(["error" => "You do not have sufficient resources to send"], Response::HTTP_BAD_REQUEST);
        }
        
        $capacityTransportShips =  $planetOrigin->transportShips * env("TRITIUM_TRANSPORTSHIP_CAPACITY");
        $totalRecursos  = $metal + $uranium + $crystal ;

        #Verificar se tem cargueiro disponível
        if ($totalRecursos > $capacityTransportShips) return response()->json(["error" => "Your freighter does not have the capacity to send all the resources"], Response::HTTP_BAD_REQUEST);

        $transportShipsInUse = floor($totalRecursos / env("TRITIUM_TRANSPORTSHIP_CAPACITY"));

        $transportShipsInUse += ($totalRecursos % env("TRITIUM_TRANSPORTSHIP_CAPACITY")) > 0 ? 1 : 0;

        $planetOrigin->transportShips -= $transportShipsInUse;
        $planetOrigin->metal -= $metal;
        $planetOrigin->uranium -= $uranium;
        $planetOrigin->crystal -= $crystal;

        $planetOrigin->save();

        #Job carregamento recursos
        $tempo_de_carregamento_carga = 5 ;
        ResourceJob::dispatch(
            $this->travelService,
            $planetOrigin->id, 
            $planetTarget->id, 
            $metal, 
            $uranium, 
            $crystal, 
            $transportShipsInUse
        )->delay(now()->addSeconds($tempo_de_carregamento_carga));

        return response()->json(["message" => "atualizados"], Response::HTTP_OK);

    }
    
}
