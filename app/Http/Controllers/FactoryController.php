<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\RobotFactoryService;
use App\Services\TransportShipsFactoryService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class FactoryController extends Controller
{
    protected $robotFactoryService;
    protected $transportShipFactoryService;

    public function __construct(RobotFactoryService $robotFactoryService, TransportShipsFactoryService $transportShipFactoryService)
    {
        $this->robotFactoryService = $robotFactoryService;
        $this->transportShipFactoryService = $transportShipFactoryService;
    }

    /**
     *
     * * @OA\Post(
     *     path="/factory/humanoid/create/{planet}/{qtd}",
     *     summary="Create humanoid in the factory",
     *     tags={"Factory"},
     *     description="Create a specified quantity of humanoid in the factory.",
     *     @OA\Parameter(
     *         name="planet",
     *         in="path",
     *         description="The planet identifier.",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="qtd",
     *         in="path",
     *         description="The quantity of humanoids to create.",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="qtd",
     *                     type="integer",
     *                     format="int32",
     *                     example="10"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Error on create humanoid"
     *     )
     * )
     *
     * @param $planet
     * @param $qtd
     * @return \Illuminate\Http\JsonResponse
     */
    public function createHumanoid($planet, $qtd)
    {
        try {
            $resp = $this->robotFactoryService->createHumanoid($planet, $qtd);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'qtd' => $resp
                ]
            ]);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error on create humanoid'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createTransportShip($planet, $qtd)
    {
        try {
            $resp = $this->transportShipFactoryService->createTransportShip($planet,$qtd);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'qtd' => $resp
                ]
            ]);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error on create transport ship'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
