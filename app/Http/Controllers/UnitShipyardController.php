<?php

namespace App\Http\Controllers;

use App\Models\UnitShipyard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class UnitShipyardController extends Controller
{

    /**
     *
     * * @OA\Get(
     *     path="/unitShipyard/list",
     *     tags={"UnitShipyard"},
     *     summary="List units",
     *     @OA\Response(
     *         response=200,
     *         description="Units retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/UnitShipyard")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error retrieving units",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error retrieving units")
     *         )
     *     )
     * )
     * @return mixed
     */
    public function list() {
        try {
            return UnitShipyard::orderBy('name')->get();
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error retrieving units'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
