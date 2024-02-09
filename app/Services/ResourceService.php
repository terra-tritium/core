<?php

namespace App\Services;

use App\Models\Logbook;
use App\Models\Planet;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ResourceService
{

    public function sendResources(Request $request)
    {
        $planetOrigin = Planet::findOrFail($request->input('origin'));
        $planetTarget = Planet::findOrFail($request->input('target'));
        if (!$planetTarget->player) return response()->json(['error' => 'Unexplored planet'], Response::HTTP_BAD_REQUEST);
        if ($planetOrigin->transportShips === 0) return response()->json(["error" => "You do not have a sufficient quantity of cargo ships"], Response::HTTP_BAD_REQUEST);
        $metal = $request->input("metal");
        $uranium = $request->input("uranium");
        $crystal = $request->input("crystal");
        if ($planetOrigin->metal < $metal) return response()->json(["error" => "You do not have sufficient resources to send"], Response::HTTP_BAD_REQUEST);
        if ($planetOrigin->uranium < $uranium) return response()->json(["error" => "You do not have sufficient resources to send"], Response::HTTP_BAD_REQUEST);
        if ($planetOrigin->crystal < $crystal) return response()->json(["error" => "You do not have sufficient resources to send"], Response::HTTP_BAD_REQUEST);
        $planetTarget->metal += $metal;
        $planetTarget->uranium += $uranium;
        $planetTarget->crystal += $crystal;

        $planetOrigin->metal -= $metal;
        $planetOrigin->uranium -= $uranium;
        $planetOrigin->crystal -= $crystal;

        $planetOrigin->save();
        $planetTarget->save();

        $this->notify($planetTarget->player, "You received a resource", "Combat");
        
        return response()->json(["message" => "atualizados"], Response::HTTP_OK);
    }
    private function notify($playerId, $text, $type)
    {
        $log = new Logbook();
        $log->player = $playerId;
        $log->text = $text;
        $log->type = $type;
        $log->save();
    }
}
