<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UnitController extends Controller
{

    /**
     *
     * * @OA\Get(
     *     path="/unit/list",
     *     tags={"Unit"},
     *     summary="List units",
     *     @OA\Response(
     *         response=200,
     *         description="Units retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/Unit")
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
            return Unit::orderBy('name')->get();
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error retrieving units'], 500);
        }
    }
}
