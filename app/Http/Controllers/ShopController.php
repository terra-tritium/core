<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Services\ShopService;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{
    public function buy($code, $planetId){
        try {
            $shopService = new ShopService;

            // if (!$rewardService->validNFT($wallet)) {
            //     return response()->json("No NFT valid", Response::HTTP_BAD_REQUEST);
            // }

            // $resultClaim = $rewardService->claim($code, $wallet, $planetId);
            // if ($resultClaim == "ok") {
            //     return response()->json($resultClaim, Response::HTTP_OK);
            // } else {
            //     return response()->json($resultClaim, Response::HTTP_BAD_REQUEST);
            // }
            
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Shop controller error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
}
