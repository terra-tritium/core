<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Services\TritiumService;

class TritiumController extends Controller
{
    public function upgrade($planetId, $building){
        try {
            $tritiumService = new TritiumService();
            $response = $tritiumService->upgrade($planetId, $building);
            if ($response == "ok") {
                return response()->json(['message' => $response], Response::HTTP_OK);
            }
            return response()->json(['message' => $response], Response::HTTP_BAD_REQUEST);
            
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Tritium miner controller error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function claim($building){
        try {
            $tritiumService = new TritiumService();
            $response = $tritiumService->claim($building);
            if ($response == "ok") {
                return response()->json(['message' => $response], Response::HTTP_OK);
            }
            return response()->json(['message' => $response], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Tritium miner controller error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
