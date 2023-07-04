<?php

namespace App\Http\Controllers;

use App\Models\Planet;
use App\Models\Player;
use App\Models\Trading;
use App\Services\TradingService;
use Illuminate\Http\Request;


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


    public function getAllTradingByMarketResource($resource, $type, $orderby = 'A', $column = '')
    {
        return $this->tradingService->getAllTradingByMarketResource($resource, $type, $orderby, $column);
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
    public function getAllOrdersPlayer($id = 'Crystal')
    {
        $planeta = $this->tradingService->getPlanetUserLogged();
        $trading = new Trading();
        $orders = $trading->getAllOrderPlayer($planeta[0]->player, $id) ?? [];
        return $orders;
    }
    public function cancelOrder($id)
    {
        return $this->tradingService->cancelOrder($id);
    }
    public function getTradingProcess($id){
        return $this->tradingService->getTradingProcess($id);
    }
    public function finishTrading(Request $request){
        return $this->tradingService->finish($request);
    }
    public function verificaTradeConcluidoSafe(){
        return $this->tradingService->verificaTradeConcluidoSafe();
    }

}
