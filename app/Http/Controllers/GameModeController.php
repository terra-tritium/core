<?php

namespace App\Http\Controllers;

use App\Models\GameMode;
use App\Models\Player;
use App\Models\Effect;
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

            $loggedInPlayer = Player::where("player", $player->id)->firstOrFail();
            $loggedInPlayer->gameMode($code);
            $this->applyEffect($player->id, $code);
            $loggedInPlayer->save();

            return response()->json(['success' => 'Modo de jogo alterado com sucesso.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(
                ['error' => 'Ocorreu um erro ao alterar o modo de jogo.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function applyEffect($player, $code)
    {
        $effect = GameMode::where("player", $player)->first();

        if (!$effect) {
            $effect = new Effect();
        }        

        switch ($code) { 
            #NFT     
            case 2:
                $effect->costBuild = -10;
                $effect->protect = 10;                
                break;    
            #Space Titan 
            case 3:
                $effect->speedProduceUnit = 20;
                $effect->extraAttack = 2;
                $effect->speedResearch = -20;
                $effect->speedMining = -20;
                break;
            # Researcher
            case 4:
                $effect->speedResearch = 20;
                $effect->costBuild = 20;
                break;
            # Engineer
            case 5:
                $effect->speedProduceShip = 20;
                $effect->speedResearch = -20;
                break;
            # Protector
            case 6:
                $effect->protect = 20;
                break;
            # Builder
            case 7:
                $effect->costBuild = -20;
                $effect->speedProduceShip = -20;
                $effect->speedProduceUnit = -20;
                break;
            # Navigator
            case 8:
                $effect->speedTravel = 20;
                $effect->speedProduceShip = -20;
                $effect->speedProduceUnit = -20;
                break;
            # Miner
            case 9:
                $effect->speedMining = 2;
                $effect->protect = -20;
                break;
        }

        $effect->save();
    }
}
