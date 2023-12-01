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


    public function getAllTradingByMarketResource($resource, $type)
    {
        return $this->tradingService->getAllTradingByMarketResource($resource, $type);
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
        return $this->tradingService->verificaAndamentoSafe();
        // return true;
    }
    public function lastTrading(){
        return $this->tradingService->getLastTrading();
    }
    public function buyFreighter($planetId){
        try{
            $planet = Planet::find($planetId);
            if(!$planet)
                return response(['message'=>"planet not found", Response::HTTP_NOT_FOUND]);
            if($planet->energy < 10)
                return response(["message"=>"Insufficient energy to buy a freighter."], Response::HTTP_NOT_FOUND); 
            $planet->energy -= 10;
            $planet->transportShips += 1;
            $planet->save();     
            return response($planet,Response::HTTP_OK);      
        }catch(Exception $e){
            Log::error('Erro ao realizar compra de cargueiro ' . $e->getMessage());
            return response(['message'=>"Erro ao realizar compra de cargueiro", Response::HTTP_INTERNAL_SERVER_ERROR]);

        }
        
        return $planet;
        // return response($, Response::HTTP_OK);
    }

}
