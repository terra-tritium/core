<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Services\RewardService;
use Illuminate\Support\Facades\Log;

class RewardController extends Controller
{
    public function claim($code, $wallet, $planetId){
        try {
            $rewardService = new RewardService;
            $resultClaim = $rewardService->claim($code, $wallet, $planetId);
            if ($resultClaim == "ok") {
                return response()->json($resultClaim, Response::HTTP_OK);
            } else {
                return response()->json($resultClaim, Response::HTTP_BAD_REQUEST);
            }
            
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Reward controller error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function verify($code) {
        $rewardService = new RewardService;
        // Verifica se o usuario já fez esse claim antes
        $result = $rewardService->verify($code);
        if ($result == true) {
            return response()->json("true", Response::HTTP_OK);
        }
        if ($result == false) {
            return response()->json("false", Response::HTTP_OK);
        }
    }
}
