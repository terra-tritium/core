<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Research;
use App\Models\Researched;
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
     *  @OA\Post(
     *     path="/research/start/{code}/{sincronize?}",
     *     tags={"Research"},
     *     summary="Start a research",
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         description="Code of the research",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sincronize",
     *         in="path",
     *         required=false,
     *         description="Flag to indicate whether to synchronize the research",
     *         @OA\Schema(type="boolean", default=false)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Research started successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Research started successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error starting research",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error starting research")
     *         )
     *     )
     * )
     *
     * @param int $code
     * @param $sincronize
     * @return string|null
     */
    public function start(int $code, $sincronize = false) {
        try {
            $player = Player::getPlayerLogged();
            $this->researchService->start($player->id, $code, $sincronize);
            return response()->json(['message' => 'Research started successfully'], 200);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error starting research'], 500);
        }
    }

    /**
     *
     * * @OA\Post(
     *     path="/research/done/{code}",
     *     tags={"Research"},
     *     summary="Mark research as done",
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         required=true,
     *         description="Code of the research",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Research marked as done successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Research marked as done successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error marking research as done",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error marking research as done")
     *         )
     *     )
     * )
     *
     * @param int $code
     * @return null
     *
     */
    public function done(int $code) {
        try {
            $player = Player::getPlayerLogged();
            $this->researchService->done($player->id, $code);

            return response()->json(['message' => 'Research completed successfully'], 200);
        } catch (\InvalidArgumentException $exception) {
            return response()->json(['message' => 'Invalid research code'], 400);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error completing research'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/research/status/{code}",
     *     tags={"Research"},
     *     summary="Get research status",
     *     description="Retrieve the status of a research for the logged-in player.",
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="Research code",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 description="Research status"
     *             ),
     *             @OA\Property(
     *                 property="progress",
     *                 type="integer",
     *                 description="Research progress percentage"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid research code",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Error message"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="Error message"
     *             )
     *         )
     *     )
     * )
     *
     * @param int $code
     * @return mixed
     */
    public function getStatus(int $code)
    {
        try {
            $player = Player::getPlayerLogged();
            $status = $this->researchService->status($player->id, $code);

            return response()->json($status, 200);
        } catch (\InvalidArgumentException $exception) {
            return response()->json(['message' => 'Invalid research code'], 400);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error retrieving research status'], 500);
        }
    }
}
