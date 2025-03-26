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
}
