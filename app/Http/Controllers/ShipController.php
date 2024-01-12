<?php

namespace App\Http\Controllers;

use App\Models\Ship;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ShipController extends Controller
{

    /**
     *
     * * @OA\Get(
     *     path="/ship/list",
     *     tags={"Ship"},
     *     summary="List units of the ships",
     *     @OA\Response(
     *         response=200,
     *         description="Ships retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Ship")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error retrieving ships",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error retrieving units")
     *         )
     *     )
     * )
     * @return mixed
     */
    public function list() {
        try {
            return Ship::orderBy('name')->get();
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error retrieving ships'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
