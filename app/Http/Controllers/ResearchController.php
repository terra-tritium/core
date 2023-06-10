<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Research;
use App\Models\Researched;
use App\Models\Planet;
use App\Services\ResearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class ResearchController extends Controller
{

    protected ResearchService $researchService;

    public function __construct(ResearchService $researchService)
    {
        $this->researchService = $researchService;
    }

    /**
     *  @OA\Get(
     *     path="/research/list",
     *     tags={"Research"},
     *     summary="Get the list of research",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Research")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error retrieving research list",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error retrieving research list")
     *         )
     *     )
     * )
     * @return JsonResponse
     */
    public function list() {
        try {
            $researchList = Research::orderBy('code')->get();
            return response()->json($researchList, 200);
        } catch (Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error retrieving research list'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/researched",
     *     tags={"Research"},
     *     summary="Get researched items",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Researched")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error getting researched items",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error getting researched items")
     *         )
     *     )
     * )
     *
     * @return mixed
     */
    public function researched() {
        try {
            $player = Player::getPlayerLogged();
            $researchedItems = Researched::where('player', $player->id)->get();
            return response()->json($researchedItems, 200);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/research/laboratory/config",
     *     tags={"Research"},
     *     summary="Config power of laboratory and synchronize researched points",
     *     @OA\Parameter(
     *         name="planet",
     *         in="path",
     *         description="Planet ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="power",
     *         in="path",
     *         description="Power of laboratory",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error researching item",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error researching item")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return mixed
     */
    public function laboratoryConfig($planet, $power) {
        try {
            $playerLogged = Player::getPlayerLogged();
            $player = Player::find($playerLogged->id);
            $planetData = Planet::find($planet);
            $result = $this->researchService->laboratoryConfig($player, $planetData, $power);
            return response()->json($result, 200);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
    
    /**
     * @OA\Post(
     *     path="/research/buy/{code}",
     *     tags={"Research"},
     *     summary="Buy research",
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="Research code",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error buying research",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error buying research")
     *         )
     *     )
     * )
     *
     * @param $code
     * @return mixed
     */
    public function buyResearch($code) {
        try {
            $player = Player::getPlayerLogged();
            $research = Research::where('code', $code)->first();
            $result = $this->researchService->buyResearch($player, $research);
            return response()->json($result, 200);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }
}
