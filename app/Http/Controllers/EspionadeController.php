<?php

namespace App\Http\Controllers;

use App\Http\Resources\Mercenary\EspionadeResource;
use App\Models\Espionage;
use App\Models\Player;
use App\Services\EspionadeService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class EspionadeController extends Controller
{

    public function __construct(private readonly  EspionadeService $espionadeService )
    {

    }

    /**
     *
     * * @OA\Get(
     *     path="/espionade/list",
     *     tags={"Ship"},
     *     summary="List espionade of the planets",
     *     @OA\Response(
     *         response=200,
     *         description="Espionade retrieved successfully",
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

            $player = Player::getPlayerLogged();
            return $this->espionadeService->list($player);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error retrieving ships'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
