<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\RobotFactoryService;
use Illuminate\Support\Facades\Log;

class FactoryController extends Controller
{
    protected $robotFactoryService;

    public function __construct(RobotFactoryService $robotFactoryService)
    {
        $this->robotFactoryService = $robotFactoryService;
    }

    /**
     *
     * @OA\Post(
     *     path="/factory/energy/{planet}/{qtd}",
     *     tags={"Factory"},
     *     summary="Set energy for a planet in the factory",
     *     @OA\Parameter(
     *         name="planet",
     *         in="path",
     *         required=true,
     *         description="Planet name or ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="qtd",
     *         in="path",
     *         required=true,
     *         description="Energy quantity",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Energy set successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="qtd", type="integer", example=100)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error setting energy",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error setting energy")
     *         )
     *     )
     * )
     *
     * @param $planet
     * @param $qtd
     * @return \Illuminate\Http\JsonResponse
     */
    public function energy($planet, $qtd)
    {
        try {
            $resp = $this->robotFactoryService->setEnergy($planet, $qtd);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'qtd' => $resp
                ]
            ]);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error setting energy'], 500);
        }
    }
}
