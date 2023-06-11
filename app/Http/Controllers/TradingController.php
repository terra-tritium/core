<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\Planet;
use App\Models\Player;
use App\Models\Trading;
use Illuminate\Http\Request;
use Exception;

class TradingController extends Controller
{

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

    private function getPlanetUserLogged()
    {
        $player = Player::getPlayerLogged();
        return Planet::where('player', $player->id)->get();
    }
    public function getAllTradingByMarketResource($resource, $type, $orderby = 'A', $column = '')
    {
        $planeta = $this->getPlanetUserLogged();
        $trading = new Trading();
        $trads = $trading->getDadosTradingByResourceAndMarket($resource, $planeta[0]->region, $type, $orderby, $column);
        return $trads;
    }
    public function getMyResources()
    {
        $planeta = $this->getPlanetUserLogged();
        $trading = new Trading();
        $resources = $trading->getMyResources($planeta[0]->player);
        return $resources;
    }
    public function tradingNewSale(Request $request)
    {
        $planeta = $this->getPlanetUserLogged();
        $trading = new Trading();
        $resources = $trading->getMyResources($planeta[0]->player)  ?? [];
        $resourceKey = strtolower($request->resource);
        if (property_exists($resources, $resourceKey)) {
            if ($resources->{$resourceKey} <= $request->quantity) {
                return ['msg' => 'tentando vender mais do que tem'];
            } else {
                $newTrading = new Trading();
                $newTrading->resource = $request->resource;
                $newTrading->type = 'S';
                $newTrading->price = $request->unitityPrice;
                $newTrading->quantity = $request->quantity;
                $newTrading->total = $request->quantity * $request->unitityPrice;
                $newTrading->status = true;
                $newTrading->idPlanetCreator = $planeta[0]->id; //pega o id do planeta que ta logado
                $newTrading->idMarket = Market::where('region','A')->first()['id'] ?? 'A'; //pega a região do planeta que ta logado
                $newTrading->save();
                return ["quantidadeDisponivel" => $resources->{$resourceKey}, "a venda" => $request->quantity, 'dados'=>$newTrading];
            }
        } else {
            return "não existe a chave informada, verificar!";
        }

        // $crys = "crystal1";
        // return  property_exists($resources,$resourceKey) ? "existe" : "não existe";
    }
}
