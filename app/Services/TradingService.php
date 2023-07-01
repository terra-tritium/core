<?php

namespace App\Services;

use App\Models\Market;
use App\Models\Planet;
use App\Models\Player;
use App\Models\Trading;
use DateTime;
use Illuminate\Http\Response;
use Exception;

class TradingService
{
    private $trading;

    public function __construct(Trading $trading)
    {
        $this->trading = $trading;
    }
    public function getPlanetUserLogged()
    {
        $player = Player::getPlayerLogged();
        return Planet::where('player', $player->id)->get();
    }
    public function getAllTradingByMarketResource($resource, $type, $orderby, $column)
    {
        $planeta = $this->getPlanetUserLogged();
        $trads = $this->trading->getDadosTradingByResourceAndMarket($resource, $planeta[0]->region, $type, $orderby, $column);
        return $trads;
    }
    public function myResources()
    {
        $planeta = $this->getPlanetUserLogged();
        $resources = $this->trading->getResourceAvailable($planeta[0]->player);
        return $resources;
    }

    private function createTrading($request, $idCreator)
    {

        try {
            $this->trading->resource = $request->resource;
            $this->trading->type = $request->type;
            $this->trading->price = $request->unitityPrice;
            $this->trading->quantity = $request->quantity;
            $this->trading->total = $request->quantity * $request->unitityPrice;
            $this->trading->status = true;
            $this->trading->idPlanetCreator = $idCreator; //pega o id do planeta que ta logado
            $this->trading->idMarket = Market::where('region', 'A')->first()['id'] ?? 'A'; //pega a região do planeta que ta logado
            $this->trading->save();
            return true;
        } catch (Exception $e) {
            //gerar o log
            return false;
        }
    }

    public function newSaleOrder($request)
    {
        if ($request) {
            $planeta = $this->getPlanetUserLogged();
            $resources = $this->trading->getResourceAvailable($planeta[0]->player)  ?? [];
            $resourceKey = strtolower($request->resource);
            if (property_exists($resources, $resourceKey)) {
                if ($resources->{$resourceKey} <= $request->quantity) {
                    return response(['error' => 'Trying to sell a quantity of resource higher than available'], Response::HTTP_BAD_REQUEST);
                } else {
                    $success = $this->createTrading($request, $planeta[0]->id);
                    if ($success) {
                        return response(['message' => 'New order successfully registered!', 'success' => true], Response::HTTP_OK);
                    } else {
                        return response(["msg" => "error "], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                }
            } else {
                return response(['message' => 'Não existe a chave informada', 'success' => false], Response::HTTP_NOT_FOUND);
            }
        }
        return response(['error' => 'Trying not registered'], Response::HTTP_BAD_REQUEST);
    }
    public function newPurchOrder($request)
    {
        if ($request) {
            try {
                if ($request->quantity <= 0 || $request->unitityPrice <= 0) {
                    return response(['message' => 'Quantidade e preço unitário devem ser superiores a 0', 'success' => false], Response::HTTP_BAD_REQUEST);
                }
                $planeta = $this->getPlanetUserLogged();
                $success = $this->createTrading($request, $planeta[0]->id);
                if ($success) {
                    return response(['message' => 'New order purch successfully registered!', 'success' => true], Response::HTTP_OK);
                } else {
                    return response(["msg" => "error "], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                return $request;
            } catch (Exception $e) {
                return response(["message" => "error " . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return response(['error' => 'Trying not registered'], Response::HTTP_BAD_REQUEST);
    }

    public function cancelOrder($id)
    {
        if ($id) {
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
                return response(['message' => 'New order sale successfully registered!', 'success' => true, 'new' => $trading], Response::HTTP_OK);
            } catch (Exception $e) {
                return response(["message" => "error " . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return response(['message' => 'Trading não encontrado', 'success' => false], Response::HTTP_NOT_FOUND);
    }
    public function getTradingProcess($id)
    {
        if ($id) {
            $this->trading = Trading::where('id', $id)
                ->where('status', 1)
                ->first();
            return $this->trading;
        }
        return response(['message' => 'Trading nao encontrada', 'success' => false], Response::HTTP_NOT_FOUND);
    }
  
    public function finish($request)
    {
        try {
            if ($request->idPlanetPurch == $request->idPlanetSale) {
                return response(['message' => 'A negociação deve ser realizada entre planetas diferentes ', 'success' => false], Response::HTTP_BAD_REQUEST);
            }
            $trading = Trading::find($request->idTrading);
            if (!$trading) {
                return response(['message' => 'Trading não encontrado', 'success' => false], Response::HTTP_NOT_FOUND);
            }
            $planeta = $this->getPlanetUserLogged();
            //verifica se o status pode ser alterado e quem ta alterando a ordem é quem criou
            if ($trading->status != 1) {
                return response(['message' => 'Status não pode ser alterado ', 'success' => false], Response::HTTP_BAD_REQUEST);
            }

            if ($request->price <= 0 || $request->quantity <= 0) {
                return response(['message' => 'Quantidade e/ou preço devem ser superior a zero', 'success' => false], Response::HTTP_BAD_REQUEST);
            }
            //verificar se tem cargueiro para buscar
            if ($planeta[0]->transportShips <= 0) {
                return response(['message' => 'Você não possui cargueiro disponível para realizar o transporte', 'success' => false], Response::HTTP_BAD_REQUEST);
            }
            /**Verificações para finalização de compra, o ativo está comprando */
            $planetaPassivo = Planet::find($request->idPlanetSale);
            $resourceKey = strtolower($request->resource);
            $quantidade = $request->quantity;
            if ($request->type == 'S') {
                //verificar se tem saldo suficiente para compra 
                if ($request->currency == 'energy') {
                    $total = $request->price * $request->quantity;
                    if ($total > $planeta[0]->energy) {
                        return response(['message' => 'Você não possui saldo suficiente para concluir a transação', 'success' => false], Response::HTTP_BAD_REQUEST);
                    }
                } else {
                    return response(['message' => 'Validar tritium', 'success' => false], Response::HTTP_BAD_REQUEST);
                }
                //verificar se o passivo ainda possui recurso suficiente para venda
                // $quantidade = 8000;
                if ($quantidade > $planetaPassivo->{$resourceKey}) {
                    $trading->status = 0;
                    $trading->updatedAt = (new DateTime())->format('Y-m-d H:i:s');
                    $trading->save();
                    //notificar o passivo que foi cancelado
                    return response(['message' => 'O vendedor não possui recurso para concluir essa transação', 'success' => false], Response::HTTP_BAD_REQUEST);
                }
            }
            if ($request->type == 'P') {
                //verificar se o ativo (vendedor) possui a quantidade de recurso
                if ($planeta[0]->{$resourceKey} < $request->quantity) {
                    return response(['message' => 'Você não possui essa quantidade de recurso para venda', 'success' => false], Response::HTTP_BAD_REQUEST);
                }
                if ($request->currency == 'energy') {
                    $total = $request->price * $request->quantity;
                    //verifica se o quem deseja comprar tem energia suficiente
                    if ($planetaPassivo->energy < $total) {
                        return response(['message' => 'O planeta comprador não possui energia suficiente para comprar', 'success' => false], Response::HTTP_BAD_REQUEST);
                    }
                } else {
                    return response(['message' => 'Validar tritium', 'success' => false], Response::HTTP_BAD_REQUEST);
                }
            }
        } catch (Exception $e) {
            return response(["message" => "error " . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $key = strtolower($request->resource);
        return response([
            'message' => 'Finish', 'success' => true,
            'planetaPassivo' => $planetaPassivo,
            'recursoNegociacao' => $planetaPassivo->{$key},
            'new' => $request->toArray(), 'planeta' => $planeta, 'currency' => $request->currency
        ], Response::HTTP_OK);
    }
}
