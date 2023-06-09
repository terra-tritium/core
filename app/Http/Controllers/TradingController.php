<?php

namespace App\Http\Controllers;

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

    private function getPlanetUserLogged(){
        $player = Player::getPlayerLogged();
        return Planet::where('player', $player->id)->get();
    }
    public function getAllTradingByMarketResource($resource, $type,$orderby = 'A',$column = ''){
        $planeta = $this->getPlanetUserLogged();
        $trading = new Trading();
        $trads = $trading->getDadosTradingByResourceAndMarket($resource, $planeta[0]->region,$type, $orderby,$column);
        return $trads;
    }

  
}
