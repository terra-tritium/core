<?php

namespace App\Http\Controllers;

use App\Models\GameMode;
use App\Models\Player;
use App\Models\Effect;
use App\Models\Planet;
use App\Services\EffectService;
use App\Services\GameModeService;
use Illuminate\Http\Response;

class GameModeController extends Controller
{

    protected $gameModeService;

    public function __construct(GameModeService $gameModeService)
    {
        $this->gameModeService = $gameModeService;
    }

    /**
     * * @OA\Get(
     *     path="/mode/list",
     *     summary="Obtém a lista de modos de jogo",
     *     operationId="getGameModes",
     *     tags={"Game Modes"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de modos de jogo",
     *         @OA\JsonContent(ref="#/components/schemas/GameMode")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao obter os modos de jogo"
     *     )
     * )
     * @return mixed
     * @throws \Exception
     */
    public function list()
    {
        // try {
        $player = Player::getPlayerLogged();
        return $this->gameModeService->list($player);
        // } catch (\Exception $e) {
        //     throw new \Exception('An error occurred while getting game modes.', 500);
        // }
    }

    /**
     * * @OA\Post(
     *     path="/mode/change/{code}",
     *     summary="Alterar o modo de jogo",
     *     operationId="changeGameMode",
     *     tags={"Game Modes"},
     *     @OA\Parameter(
     *         name="code",
     *         in="path",
     *         description="Código do modo de jogo",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso ao alterar o modo de jogo",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="string",
     *                 example="Modo de jogo alterado com sucesso."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao alterar o modo de jogo"
     *     )
     * )
     *
     * @param $code
     * @return void
     */
    public function change($code)
    {
        try {
            $player = Player::getPlayerLogged();
            $loggedInPlayer = Player::where("id", $player->id)->firstOrFail();
            $effectService = app(EffectService::class);
            return $effectService->applyEffect($loggedInPlayer, $code);

            // $loggedInPlayer->save();
            return response()->json(['success' => 'Modo de jogo alterado com sucesso.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(
                ['error' => 'Ocorreu um erro ao alterar o modo de jogo. '.$e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function gameModeEffectPlayer($player){
        try {
            $player = Player::findOrFail($player);
            $effect = Effect::where("player",$player->id)->firstOrFail();
            $effect->gameMode = $player->gameMode;
            return response()->json($effect, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(
                ['error' => 'Ocorreu um erro ao recuperar effeito do tipo de jogo.'.$e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function gameModeEffect($planet){
        try {
            $planet = Planet::findOrFail($planet);
            $player = Player::findOrFail($planet->player);
            $effect = Effect::where("player",$player->id)->firstOrFail();
            $effect->gameMode = $player->gameMode;
            return response()->json($effect, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(
                ['error' => 'Ocorreu um erro ao recuperar effeito do tipo de jogo.'.$e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
