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

            $resultBuy = $shopService->buy($code, $planetId);

            if ($resultBuy == "ok") {
                return response()->json($resultBuy, Response::HTTP_OK);
            } else {
                return response()->json($resultBuy, Response::HTTP_BAD_REQUEST);
            }
            
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Shop controller error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function redeem($wallet, $collection, $token_id){
        try {
            $shopService = new ShopService;

            $resultRedeem = $shopService->redeem($wallet, $collection, $token_id);

            if ($resultRedeem == "ok") {
                return response()->json($resultRedeem, Response::HTTP_OK);
            } else {
                return response()->json($resultRedeem, Response::HTTP_BAD_REQUEST);
            }
            
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Shop controller error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
