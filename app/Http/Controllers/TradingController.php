<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\Planet;
use App\Models\Player;
use App\Models\Trading;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $resources = $trading->getResourceAvailable($planeta[0]->player);
        return $resources;
        // return $resources;
    }
    public function tradingNewSale(Request $request)
    {
        $planeta = $this->getPlanetUserLogged();
        $trading = new Trading();
        $resources = $trading->getResourceAvailable($planeta[0]->player)  ?? [];
        $resourceKey = strtolower($request->resource);
        if (property_exists($resources, $resourceKey)) {
            if ($resources->{$resourceKey} <= $request->quantity) {
                return response()->json(['error' => 'Trying to sell a quantity of resource higher than available'], Response::HTTP_BAD_REQUEST);
            } else {
                try {
                    $newTrading = new Trading();
                    $newTrading->resource = $request->resource;
                    $newTrading->type = 'S';
                    $newTrading->price = $request->unitityPrice;
                    $newTrading->quantity = $request->quantity;
                    $newTrading->total = $request->quantity * $request->unitityPrice;
                    $newTrading->status = true;
                    $newTrading->idPlanetCreator = $planeta[0]->id; //pega o id do planeta que ta logado
                    $newTrading->idMarket = Market::where('region', 'A')->first()['id'] ?? 'A'; //pega a região do planeta que ta logado
                    $newTrading->save();
                    return response(['message' => 'New order successfully registered!', 'success' => true, 'new' => $newTrading], Response::HTTP_OK);
                } catch (Exception $e) {
                    return response(["msg" => "error " . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }
        } else {
            return response(['message' => 'Não existe a chave informada', 'success' => false], Response::HTTP_NOT_FOUND);
        }
    }

    public function getAllOrdersPlayer()
    {
        $planeta = $this->getPlanetUserLogged();
        $trading = new Trading();
        $orders = $trading->getAllOrderPlayer($planeta[0]->player) ?? [];
        return $orders;
    }
    public function cancelOrder($id)
    {
        try {
            $trading = Trading::find($id);
            if (!$trading) {
                return response(['message' => 'Trading não encontrado', 'success' => false], Response::HTTP_NOT_FOUND);
            }
            $planeta = $this->getPlanetUserLogged();
            //verifica se o status pode ser alterado e quem ta alterando a ordem é quem criou
            if ($trading->status != 1 || $trading->idPlanetCreator != $planeta[0]->player) {
                return response(['message' => 'Status não pode ser alterado ou não é o criador', 'success' => false], Response::HTTP_BAD_REQUEST);
            }
            $trading->status = 0;
            $trading->updatedAt = (new DateTime())->format('Y-m-d H:i:s');
            $trading->save();
            return response(['message' => 'New order successfully registered!', 'success' => true, 'new' => $trading], Response::HTTP_OK);
        } catch (Exception $e) {
            return response(["message" => "error " . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
