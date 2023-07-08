<?php

namespace App\Services;

use App\Models\Market;
use App\Models\Planet;
use App\Models\Player;
use App\Models\Safe;
use App\Models\Trading;
use App\Models\TradingFinished;
use DateTime;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\DB;

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
                $trading->status = config('MARKET_STATUS_CANCELED');
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
        $status = config('MARKET_STATUS_OPEN');
        if ($id) {
            $this->trading = Trading::where('id', $id)
                ->where('status', $status)
                ->first();
            return $this->trading;
        }
        return response(['message' => 'Trading nao encontrada', 'success' => false], Response::HTTP_NOT_FOUND);
    }

    public function finish($request)
    {
        $panetaInteressado = 0;

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
                return response(['message' => 'Essa ordem não está mais disponível ', 'code' => 4001, 'success' => false], Response::HTTP_BAD_REQUEST);
            }

            if ($request->price <= 0 || $request->quantity <= 0) {
                return response(['message' => 'Quantidade e/ou preço devem ser superior a zero', 'code' => 4002, 'success' => false], Response::HTTP_BAD_REQUEST);
            }
            //verificar se tem cargueiro para buscar
            if ($planeta[0]->transportShips <= 0) {
                return response(['message' => 'Você não possui a quantidade necessária de cargueiros para realizar o transporte', 'code' => 4003, 'success' => false], Response::HTTP_BAD_REQUEST);
            }
            /**Verificações para finalização de compra, o ativo está comprando */
            $planetaPassivo = Planet::find($request->idPlanetSale);
            $resourceKey = strtolower($request->resource);
            $quantidade = $request->quantity;
            //S pq o passivo esta vendendo e o ativo comprando
            if ($request->type == 'S') {
                $panetaInteressado = $request->idPlanetPurch;
                //verificar se tem saldo suficiente para compra 
                if ($request->currency == 'energy') {
                    $total = $request->price * $request->quantity;
                    if ($total > $planeta[0]->energy) {
                        return response(['message' => 'Você não possui saldo suficiente para concluir a transação', 'code' => 4004, 'success' => false], Response::HTTP_BAD_REQUEST);
                    }
                } else {
                    return response(['message' => 'Validar tritium', 'success' => false], Response::HTTP_BAD_REQUEST);
                }
                //verificar se o passivo ainda possui recurso suficiente para venda
                // $quantidade = 8000;
                if ($quantidade > $planetaPassivo->{$resourceKey}) {
                    $status = config('MARKET_STATUS_CANCELED');
                    $trading->status = $status;
                    $trading->updatedAt = (new DateTime())->format('Y-m-d H:i:s');
                    $trading->save();
                    //notificar o passivo que foi cancelado
                    return response(['message' => 'O vendedor não possui recurso para concluir essa transação', 'code' => 4005, 'success' => false], Response::HTTP_BAD_REQUEST);
                }
                if (!$this->safe($trading, $request)) {
                    return response(['message' => 'Algum erro na hora de comprar, verificar a causa', 'code' => 4006, 'success' => false], Response::HTTP_BAD_REQUEST);
                }
            }
            //P pq o passivo esta comprando e o ativo vendendo
            if ($request->type == 'P') {
                $panetaInteressado = $request->idPlanetSale;
                //verificar se o ativo (vendedor) possui a quantidade de recurso
                if ($planeta[0]->{$resourceKey} < $request->quantity) {
                    return response(['message' => 'Você não possui essa quantidade de recurso para venda', 'code' => 4007, 'success' => false], Response::HTTP_BAD_REQUEST);
                }
                if ($request->currency == 'energy') {
                    $total = $request->price * $request->quantity;
                    //verifica se o quem deseja comprar tem energia suficiente
                    if ($planetaPassivo->energy < $total) {
                        return response(['message' => 'O planeta comprador não possui energia suficiente para comprar', 'code' => 4008, 'success' => false], Response::HTTP_BAD_REQUEST);
                    }
                } else {
                    return response(['message' => 'Validar tritium', 'success' => false], Response::HTTP_BAD_REQUEST);
                }

                if (!$this->safe($trading, $request)) {
                    return response(['message' => 'Algum erro na hora de vender, verificar a causa', 'code' => 4009, 'success' => false], Response::HTTP_BAD_REQUEST);
                }
            }
        } catch (Exception $e) {
            return response(["message" => "error " . $e->getMessage(), "code" => 4010], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response([
            'message' => 'Finish', 'success' => true,
            'planetaPassivo' => $planetaPassivo,
            'panetaInteressado' => $panetaInteressado,
            'new2' => $request->toArray(), 'planeta' => $planeta, 'currency' => $request->currency
        ], Response::HTTP_OK);
    }

    public function safe(Trading $trading, $request)
    {
        try {
            $planetaInteressado = $request->type == 'S' ? $request->idPlanetPurch : $request->idPlanetSale;
            $success = $this->atualizaStatusTrading($trading, $planetaInteressado);
            if ($success) {
                $successSafe = $this->saveSafe($request, $trading->idMarket, $trading->idPlanetCreator, 1);
                $successDebito = $this->debitarSaldosPlaneta($request);
                return ($successDebito && $successSafe);
            }
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * @todo alterar o currency quando tiver tratando com tritium
     */
    private function atualizaStatusTrading(Trading $trading, $planetaInteressado)
    {
        $status = config('MARKET_STATUS_PENDING');

        $trading->idPlanetInterested = $planetaInteressado;
        $trading->status = $status;
        $trading->currency = 'energy'; //default
        $trading->updatedAt = (new DateTime())->format('Y-m-d H:i:s');
        return $trading->save();
    }
    /**
     * @todo calcular a distancia
     */
    private function saveSafe($request, $idMarket, $planetCreator, $transportShips = 1)
    {
        $status = config('MARKET_STATUS_PENDING');
        try {
            $safe = new Safe();
            $safe->idPlanetSale = $request->idPlanetSale;
            $safe->idPlanetPurch = $request->idPlanetPurch;
            $safe->idPlanetCreator = $planetCreator;
            $safe->status = $status;
            $safe->deliveryTime = 50;
            $safe->type = $request->type;
            $safe->currency = $request->currency;
            $safe->resource = strtolower($request->resource);
            $safe->quantity = $request->quantity;
            $safe->price = $request->price;
            $safe->total = $request->price * $request->quantity;
            $safe->idMarket = $idMarket;
            $safe->idTrading = $request->idTrading;
            $safe->transportShips = $transportShips;
            $safe->distance = 5000;
            $safe->save();
            return $safe->save();
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * @todo fazer a logica com negociação envolvendo tritium e quantidade de cargueiros
     */
    private function debitarSaldosPlaneta($request)
    {
        $idPlanetaPassivo = 0;
        $idPlanetaAtivo = 0;
        try {
            $keyResource = strtolower($request->resource);
            if ($request->type == 'S') {
                $planetaPassivo = Planet::find($request->idPlanetSale);
                $planetaAtivo = Planet::find($request->idPlanetPurch);
                //planeta passivo (vendedor) subtrai o recurso que está sendo vendido
                $planetaPassivo->{$keyResource} = ($planetaPassivo->{$keyResource} - $request->quantity);
                if ($request->currency == 'energy') {
                    $planetaAtivo->energy = $planetaAtivo->energy - ($request->quantity * $request->price);
                }
                $planetaAtivo->transportShips = $planetaAtivo->transportShips - 1;
                $planetaPassivo->save();
                $planetaAtivo->save();
            } else {
                $idPlanetaPassivo = $request->idPlanetPurch;
                $idPlanetaAtivo = $request->idPlanetSale;
                $planetaPassivo = Planet::find($idPlanetaPassivo);
                $planetaAtivo = Planet::find($idPlanetaAtivo);
                if ($request->currency == 'energy') {
                    $planetaPassivo->energy = $planetaPassivo->energy - ($request->quantity * $request->price);
                }
                $planetaAtivo->{$keyResource} = $planetaAtivo->{$keyResource} - $request->quantity;
                $planetaAtivo->transportShips = $planetaAtivo->transportShips - 1;
                $planetaAtivo->save();
                $planetaPassivo->save();
            }
            return true; //corrigir o que ta retornando
        } catch (Exception $e) {
            return false;
        }
    }
    private function deleteTradingConcluidos($concluidos)
    {
        if ($concluidos) {
            foreach ($concluidos as $concluido) {
                $trading = Trading::find($concluido->idTrading);
                $safe = Safe::where('idTrading', $concluido->idTrading)->first();
                if ($safe)
                    $safe->delete();
                if ($trading) 
                    $trading->delete();
            }
        }
    }
    public function verificaTradeConcluidoSafe()
    {
        $safe = new Safe();
        $dadosSafe = $safe->getDadosSafe();
        $filtrado = $this->getDeliveryTimeConclued($dadosSafe);
        $atualizar = $this->atualizaStatusTradingConclued($filtrado['concluido']);
        //debitar e creditar valores para os usuarios
        $executados = $this->updateResourceTradeConclued($filtrado['concluido']);
        //deletar da tabela trading e deixar apenas na finish
        //deletar da safe
        $this->deleteTradingConcluidos($filtrado['concluido']);
        return response([
            'message' => 'Finish', 'success' => true,
            'info' => $dadosSafe,
            'filter' => $filtrado,
            'atualizarq' => $atualizar,
            'executados' => $executados
        ], Response::HTTP_OK);
    }
    /**
     * @todo colocar o calculo de distancia
     *   Se o planeta é o planeta vendedor, o recurso não volta para ele, ele apenas recebe o pagamento em energia
     *  o recurso irá sair da safe e irá para o comprador, no momento do inicio da transação foi debitado o recurso de seus cofres
     *
     *   se o planeta é o comprador, a energia não volta para ele, ele irá receber a quantidade de recurso comprado
     *   e o vendedor receberar a energia, aumenta a quantidade de recursos em seus cofres mas nesse momento não toca na energia
     */
    private function updateResourceTradeConclued($concluidos)
    {
        $compradores = [];
        $vendedores = [];
        $cargueiros = [];
        if ($concluidos) {
            foreach ($concluidos as $conc) {
                if ($conc->type === 'S') {
                    $planetaVendedor = Planet::find($conc->idPlanetSale);
                    $planetaVendedor->energy += ($conc->quantity * $conc->price);
                    $vendedores[] = $planetaVendedor;
                    $planetaVendedor->save();
                } else {
                    $keyRecurso = $conc->resource;
                    $planetaComprador = Planet::find($conc->idPlanetPurch);
                    $planetaComprador->{$keyRecurso} = ($planetaComprador->{$keyRecurso} + $conc->quantity);
                    $compradores[] = $planetaComprador;
                    $planetaComprador->save();
                }
                /**Devolve o cargueiro para o ativo */
                if ($conc->idPlanetPurch == $conc->idPlanetInterested) {
                    $cargueiros[] = $conc->idPlanetInterested;
                    $cargueiros = $conc->transportShips;
                    DB::table('planets')->where('id', $conc->idPlanetInterested)->update([
                        'transportShips' => DB::raw("transportShips + $cargueiros")
                    ]);
                }
            }
        }
        /** 
         *@todo retirar o retorno, apenas para fins de logs 
         */
        return ['compradores ' => $compradores, 'vendedores ' => $vendedores, 'cargueirosid' => $cargueiros];
    }
    /**
     * @todo colocar o calculo de distancia
     */
    private function atualizaStatusTradingConclued($concluidos)
    {
        $status = config('MARKET_STATUS_FINISHED');
        try {
            foreach ($concluidos as $c) {
                $finished = new TradingFinished();
                $finished->createdAt = $c->createdAt;
                $finished->idPlanetCreator = $c->idPlanetCreator;
                $finished->idPlanetInterested = $c->idPlanetInterested;
                $finished->quantity = $c->quantity;
                $finished->price = $c->price;
                $finished->distance = $c->distance;
                $finished->deliveryTime = $c->deliveryTime;
                $finished->idTrading = $c->id;
                $finished->status = $status; //concluido
                $finished->currency = $c->currency;
                $finished->type = $c->type;
                $finished->idMarket = $c->idMarket;
                $finished->resource = $c->resource;
                $finished->transportShips = $c->transportShips;
                $finished->finishedAt = $c->tempoFinal;
                return $finished->save();
            }
        } catch (Exception $e) {
            return response(["message" => "error finished trading" . $e->getMessage(), "code" => 4010], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    private function getDeliveryTimeConclued($dadosSafe)
    {
        $concluidos = [];
        $naoConcluidos = [];
        if ($dadosSafe) {
            foreach ($dadosSafe as $dados) {
                if ($dados->concluido) {
                    $concluidos[] = $dados;
                } else {
                    $naoConcluidos[] = $dados;
                }
            }
        }
        return ['concluido' => $concluidos, 'naoConcluido' => $naoConcluidos];
    }
}
