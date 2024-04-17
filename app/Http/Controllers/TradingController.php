<?php

namespace App\Http\Controllers;

use App\Models\Planet;
use App\Models\Player;
use App\Models\Trading;
use App\Services\TradingService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;



class TradingController extends Controller
{

    private $tradingService;

    public function __construct(TradingService $tradingService)
    {
        $this->tradingService = $tradingService;
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Planet  $planet
     * @return \Illuminate\Http\Response
     */
    public function show(Planet $planet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Planet  $planet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Planet $planet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Planet  $planet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Planet $planet)
    {
        //
    }

    public function list()
    {
        $player = Player::getPlayerLogged();
        return Planet::where('player', $player->id)->get();
    }


    public function getAllTradingByMarketResource($planetId, $resource, $type)
    {
        return $this->tradingService->getAllTradingByMarketResource($planetId, $resource, $type);
    }

    public function getPlayerResource($planet)
    {
        try {
            $loggedPlayer = Player::getPlayerLogged();
            if (!$loggedPlayer) {
                return response()->json(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
            }
            $planet = Planet::join('players as player', 'player.id', '=', 'planets.player')->where('planets.id', $planet)->first();
            $retorno['metal'] = $planet->metal;
            $retorno['uranium'] = $planet->uranium;
            $retorno['crystal'] = $planet->crystal;
            $retorno['energy'] = $planet->energy;
            $retorno['player'] = $planet->player;
            $retorno['transportShips'] = $planet->transportShips;
            return response()->json($retorno, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'erro ao recuperar recursos do jogador ' . $e->getMessage(), 'success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function getMyResources()
    {
        return $this->tradingService->myResources();
    }

    public function tradingNewSale(Request $request)
    {
        return $this->tradingService->newSaleOrder($request);
    }
    public function tradingNewPurchase(Request $request)
    {
        return $this->tradingService->newPurchOrder($request);
    }
    //subir
    public function getAllOrderByPlanet($planet, $id = 'Crystal')
    {
        $trading = new Trading();
        $orders = $trading->getAllOrderByPlanet($planet, $id) ?? [];
        return $orders;
    }
    public function cancelOrder($planet, $id)
    {
        return $this->tradingService->cancelOrder($planet, $id);
    }
    public function getTradingProcess($planetId, $id)
    {
        return $this->tradingService->getTradingProcess($planetId,$id);
    }
    public function finishTrading(Request $request)
    {
        return $this->tradingService->finish($request);
    }
    public function verificaTradeConcluidoSafe()
    {
        return $this->tradingService->verificaAndamentoSafe();
        // return true;
    }
    public function lastTrading()
    {
        return $this->tradingService->getLastTrading();
    }
}
