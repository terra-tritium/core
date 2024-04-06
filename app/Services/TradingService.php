<?php

namespace App\Services;

// use App\Http\Controllers\LogbookController;

use App\Jobs\ResourceJob;
use App\Jobs\TravelJob;
use App\Models\Logbook;
use App\Models\Market;
use App\Models\Planet;
use App\Models\Player;
use App\Models\Position;
use App\Models\ProcessJob;
use App\Models\Safe;
use App\Models\Trading;
use App\Models\TradingFinished;
use App\Models\Travel;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class TradingService
{
    public function __construct(
        protected readonly Trading $trading,

        protected readonly PlanetService $planetService
    ) {
    }

    public function getPlanetUserLogged()
    {
        $player = Player::getPlayerLogged();
        return Planet::where('player', $player->id)->get();
    }
    public function getAllTradingByMarketResource($resource, $type)
    {
        $planeta = $this->getPlanetUserLogged();
        $trads = $this->trading->getDadosTradingByResourceAndMarket($planeta[0]->id, $resource, $planeta[0]->region, $type);
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
            Log::error('Erro ao criar uma nova ordem: ' . $e->getMessage());
            return false;
        }
    }

    public function newSaleOrder($request)
    {
        try {
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
                            $this->notify($planeta[0]->player, "New order successfully registered", "Market");
                            return response(['message' => 'New order successfully registered!', 'success' => true], Response::HTTP_OK);
                        } else {

                            return response(["msg" => "error "], Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                    }
                } else {
                    return response(['message' => 'Não existe a chave informada', 'success' => false], Response::HTTP_NOT_FOUND);
                }
            }
        } catch (\Exception $e) {
            return response(['error' => 'Trying not registered ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
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
                    $this->notify($planeta[0]->player, "New order purch successfully registered", "Market");
                    return response(['message' => 'New order purch successfully registered!', 'success' => true], Response::HTTP_OK);
                } else {
                    return response(["msg" => "error "], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                return $request;
            } catch (Exception $e) {
                Log::error('Erro na abertura de nova ordem: ' . $e->getMessage());

                return response(["message" => "error " . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return response(['error' => 'Trying not registered'], Response::HTTP_BAD_REQUEST);
    }

    public function cancelOrder($planet, $id)
    {
        if ($id) {
            try {
                $trading = Trading::find($id);
                if (!$trading) {
                    return response(['message' => 'Trading não encontrado', 'success' => false], Response::HTTP_NOT_FOUND);
                }
                $planeta = $this->getPlanetUserLogged();
                //verifica se o status pode ser alterado e quem ta alterando a ordem é quem criou
                if ($trading->status != config("app.tritium_market_status_open") || $trading->idPlanetCreator != $planet) {
                    return response(['message' => 'Status não pode ser alterado ou não é o criador', 'success' => false], Response::HTTP_BAD_REQUEST);
                }
                $trading->status = config("app.tritium_market_status_canceled");
                $trading->updatedAt = (new DateTime())->format('Y-m-d H:i:s');
                $trading->save();
                $this->notify($planeta[0]->player, "You canceled your order", "Market");
                return response(['message' => 'You canceled your order!', 'success' => true, 'new' => $trading], Response::HTTP_OK);
            } catch (Exception $e) {
                Log::error('Erro no cancelamento da ordem: ' . $e->getMessage());
                return response(["message" => "error " . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return response(['message' => 'Trading não encontrado', 'success' => false], Response::HTTP_NOT_FOUND);
    }
    public function getTradingProcess($id)
    {
        try {
            if ($id) {
                $trade = Trading::where('id', $id)
                    ->where('status', config("app.tritium_market_status_open"))
                    ->first();
                return response()->json($trade, Response::HTTP_OK);
            }
            return response()->json(['message' => 'Trading nao encontrada', 'success' => false], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['message' => 'erro ao recuperar processo' . $e->getMessage(), 'success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
            if ($trading->status != config("app.tritium_market_status_open")) {
                return response(['message' => 'Essa ordem não está mais disponível ', 'code' => 4001, 'success' => false], Response::HTTP_BAD_REQUEST);
            }
            if ($request->price <= 0 || $request->quantity <= 0) {
                return response(['message' => 'Quantidade e/ou preço devem ser superior a zero', 'code' => 4002, 'success' => false], Response::HTTP_BAD_REQUEST);
            }
            $planeta = $this->getPlanetUserLogged();
            $player = Player::getPlayerLogged();

            //verificar se tem cargueiro para buscar
            if ($player->transportShips <= 0) {
                return response(['message' => 'Você não possui a quantidade necessária de cargueiros para realizar o transporte', 'code' => 4003, 'success' => false], Response::HTTP_BAD_REQUEST);
            }
            $planetaPassivo = Planet::find($request->idPlanetSale);
            $resourceKey = strtolower($request->resource);
            $quantidade = $request->quantity;

            //S pq o passivo esta vendendo e o ativo comprando, ativo tem que buscar
            if ($request->type == 'S') {
                $panetaInteressado = $request->idPlanetPurch;
                //verificar se tem saldo suficiente para compra
                if ($request->currency == 'energy') {
                    $total = $request->price * $request->quantity;
                    if ($total > $planeta[0]->energy) {
                        $this->notify($planeta[0]->player, "saldo insuficiente para concluira a transação", "Market");
                        return response(['message' => 'Você não possui saldo suficiente para concluir a transação', 'code' => 4004, 'success' => false], Response::HTTP_BAD_REQUEST);
                    }
                } else {
                    return response(['message' => 'Validar tritium', 'success' => false], Response::HTTP_BAD_REQUEST);
                }

                if ($quantidade > $planetaPassivo->{$resourceKey}) {
                    $status = config("app.tritium_market_status_canceled");
                    $trading->status = $status;
                    $trading->updatedAt = (new DateTime())->format('Y-m-d H:i:s');
                    $trading->save();
                    //notificar o passivo que foi cancelado
                    $this->notify($planetaPassivo->player, "O vendedor não possui recurso para concluir essa transação", "Market");
                    return response(['message' => 'O vendedor não possui recurso para concluir essa transação', 'code' => 4005, 'success' => false], Response::HTTP_BAD_REQUEST);
                }
                $distance = $this->planetService->calculeDistance($planeta[0]->id, $planetaPassivo->id);


                #teste travel trade
                return $this->safe($trading, $request, $distance, $planeta[0]->player, $planetaPassivo->player);
                // Sair para a viagem antes de salvar na safe
                if (!$this->safe($trading, $request, $distance, $planeta[0]->player, $planetaPassivo->player)) {
                    return response(['message' => 'Algum erro na hora de comprar, verificar a causa', 'code' => 4006, 'success' => false], Response::HTTP_BAD_REQUEST);
                }
                $this->notify($planeta[0]->player, 'Seu cargueiro saiu para buscar o recurso', 'Market');
                $this->notify($planetaPassivo->player, 'O comprador está vindo em sua direção', 'Market');
                //retorno visual, deletar
                return response(['Ativo' => $planeta[0], 'passivo' => $planetaPassivo, 'tipo' => $request->type, 'desc' => "ativo tem que ir buscar "]);
            } else {
                $planetaPassivo = Planet::find($request->idPlanetPurch);
                $panetaInteressado = $request->idPlanetSale;
                //verificar se o ativo (vendedor) possui a quantidade de recurso
                if ($planeta[0]->{$resourceKey} < $request->quantity) {
                    $this->notify($planeta[0]->player, "Você não possui essa quantidade de recurso para venda", "Market");
                    return response(['message' => 'Você não possui essa quantidade de recurso para venda', 'code' => 4007, 'success' => false], Response::HTTP_BAD_REQUEST);
                }
                if ($request->currency == 'energy') {
                    $total = $request->price * $request->quantity;
                    //verifica se o quem deseja comprar tem energia suficiente
                    if ($planetaPassivo->energy < $total) {
                        $this->notify($planetaPassivo->player, "O planeta comprador não possui energia suficiente para comprar", "Market");
                        return response(['message' => 'O planeta comprador não possui energia suficiente para comprar', 'code' => 4008, 'success' => false], Response::HTTP_BAD_REQUEST);
                    }
                } else {
                    return response(['message' => 'Validar tritium', 'success' => false], Response::HTTP_BAD_REQUEST);
                }
                $distance = $this->planetService->calculeDistance($planeta[0]->id, $planetaPassivo->id);
                if (!$this->safe($trading, $request, $distance, $planetaPassivo->player, $planeta[0]->player)) {
                    return response(['message' => 'Algum erro na hora de vender, verificar a causa', 'code' => 4009, 'success' => false], Response::HTTP_BAD_REQUEST);
                }
                //retorno visual, deletar
                $this->notify($planeta[0]->player, 'Seu cargueiro saiu para buscar o recurso ?', 'Market');
                $this->notify($planetaPassivo->player, 'O comprador está vindo em sua direção ?', 'Market');
                return response(['Ativo' => $planeta[0], 'passivo' => $planetaPassivo, 'tipo' => $request->type, 'desc' => "ativo tem que entregra de imediato"]);
            }
        } catch (Exception $e) {
            return response(["message" => "error " . $e->getMessage(), "code" => 4010], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response([
            'message' => 'trade ', 'Requisicao' => $request->toArray(),
            'trade' => $trading
        ], Response::HTTP_OK);
    }

    /**
     * $from Ativo
     * $to passivo
     */
    public function calcDistance($from, $to)
    {
        $positionFrom = $this->convertPosition($from);
        $positionTo = $this->convertPosition($to);
        if (!($positionFrom && $positionTo)) {
            return false;
        }
        $diffRegion = abs(ord($positionFrom->region) - ord($positionTo->region));
        $diffQuadrant = abs($positionFrom->quadrant - $positionTo->quadrant);
        $diffPosition = abs($positionFrom->position - $positionTo->position);
        // return ['diiffRegion' =>$diffRegion, 'diffQuadrant' => $diffQuadrant, 'diffPosition' => $diffPosition, 'calc' =>($diffRegion * 100) + ($diffQuadrant * 10) + $diffPosition];
        return ($diffRegion * 100) + ($diffQuadrant * 10) + $diffPosition;
    }

    public function convertPosition($location)
    {
        $position = new Position();
        $position->region = substr($location->region, 0, 1);
        $position->quadrant = substr($location->quadrant, 1, 3);
        $position->quadrant_full = substr($location->quadrant, 0, 4);
        $position->position = $location->position;
        if (!$this->isValidRegion($position->region)) {
            return false;
        }
        if (!$this->isValidQuadrant($position->quadrant)) {
            return false;
        }
        if (!$this->isValidPosition($position->position)) {
            return false;
        }

        return $position;
    }

    public function isValidRegion($letter)
    {
        $valorAscii = ord($letter);
        $valorAsciiA = ord('A');
        $valorAsciiP = ord('P');
        if ($valorAscii >= $valorAsciiA && $valorAscii <= $valorAsciiP) {
            return true;
        } else {
            return false;
        }
    }

    public function isValidQuadrant($quadrant)
    {
        if ($quadrant >= 0 && $quadrant < 100) {
            return true;
        } else {
            return false;
        }
    }

    public function isValidPosition($position)
    {
        if ($position > 0 && $position <= 16) {
            return true;
        } else {
            return false;
        }
    }

    public function safe(Trading $trading, $request, $distancia, $origem, $destino)
    {
        $planetaOrigem = $request->type == 'P' ? $request->idPlanetSale : $request->idPlanetPurch;
        $planetaDestino = $request->type == 'S' ? $request->idPlanetSale : $request->idPlanetPurch;
        $now = time();

        // return ['JogadorOrigem' => $origem, 'JogadorDestino' => $destino, 
        //         'PlanetaOrigem' => $planetaOrigem, 'PlanetaDestino' => $planetaDestino, 'trading' => $trading,
        //         'cargueiros'=> ceil($trading->quantity / config("app.tritium_transportship_capacity"))];
        // // $playerOrigem = Player::
        // return ['request' => $request->toArray(), 'distancia' => $distancia];
        try {
            $transportShipsInUse = ceil($trading->quantity / config("app.tritium_transportship_capacity")); //TRITIUM_TRANSPORTSHIP_CAPACITY

            $timeLoad = $transportShipsInUse * config("app.tritium_charging_speed");

            #Salva o job para acompanhamento até a execução
            $processJob = new ProcessJob();
            $processJob->player = $origem;
            $processJob->planet = $planetaOrigem;
            $processJob->finished =  Carbon::now()->addSeconds($timeLoad)->getTimestamp();
            $processJob->type = ProcessJob::TYPE_CARRYING;
            $processJob->save();
            // return ["process"=>$processJob];
            $travelService = app(TravelService::class);
            #Job carregamento recursos
            // ResourceJob::dispatch(
            //     $travelService,
            //     $origem,
            //     $planetaOrigem,
            //     $planetaDestino,
            //     1,
            //     2,
            //     3,
            //     $transportShipsInUse
            // )->delay(now()->addSeconds($timeLoad));





            /**Inicio */
            $travel = new Travel();
            $travel->player = $origem;
            $travel->receptor = $destino;
            $travel->from = $planetaOrigem;
            $travel->to = $planetaDestino;
            /**Lógica invertida, quando é venda, o comprador ta indo buscar */
            $travel->action = $request->type == 'S' ? Travel::TRANSPORT_BUY : Travel::TRANSPORT_SELL;
            $travel->transportShips = $transportShipsInUse;
            $travel->start = $now;
            $travelTime = $distancia;
            $travel->arrival = $now + $travelTime;
            $travel->status = Travel::STATUS_ON_GOING;
            $travel->trading = $trading->id;
            $successt = $travel->save();
            #debita a quantidade de cargueiro que será usado
            $player = Player::find($travel->player);
            $player->transportShips -= $transportShipsInUse;
            $player->save();


            TravelJob::dispatch($travelService, $travel->id, false)->delay(now()->addSeconds(60));

            return ['travel' => $travel, 'success' => $successt];
            /**Fim */
            $planetaInteressado = $request->type == 'S' ? $request->idPlanetPurch : $request->idPlanetSale;
            $success = $this->atualizaStatusTrading($trading, $planetaInteressado);
            if ($success) {
                $successSafe = $this->saveSafe($request, $trading->idMarket, $trading->idPlanetCreator, $distancia, 1);
                $successDebito = $this->debitarSaldosPlaneta($request, 'inicio');
                return $successDebito;
                return ($successDebito && $successSafe);
            }
        } catch (Exception $e) {
            return "erro " . $e->getMessage();
        }
    }
    /**
     * Cargueiro chegou no destino, deve realizar a retirada de recurso e iniciar viagem de volta
     */
    public function realizaCompra(TravelService $travelService, Travel $travel)
    {
        Log::info("foi realizar a compra, esta indo, ao finalizar retornar - ajustar o tempo 2");
        $trading = Trading::find($travel->trading);
        #Retirar do vendedor a quantidade de recurso necessária e entrega a energia - OK
        if ($this->atualizaRecursoVendedor($trading)) {
            if ($trading->resource == 'Metal') {
                $travel->metal = $trading->quantity;
            }
            if ($trading->resource == 'Crystal') {
                $travel->crystal = $trading->quantity;
            }
            if ($trading->resource == 'Uranium') {
                $travel->uranium = $trading->quantity;
            }
        } else {
            #Notificar
            Log::info("notificar a situação, vendedor nao tem mais o recurso disponível");
        }

        $travel->status = Travel::STATUS_RETURN;
        $travel->save();
        #atualizar situação trading
        TravelJob::dispatch($travelService, $travel->id, true)->delay(now()->addSeconds(60));
    }
    private function atualizaRecursoVendedor(Trading $trading)
    {
        if ($trading->type == 'S') {
            $planet = Planet::find($trading->idPlanetCreator);
            $keyResource = strtolower($trading->resource);
            #sincronizar
            if ($planet->{$keyResource} >= $trading->quantity) {
                $planet->{$keyResource} -= $trading->quantity;
                $planet->energy += $trading->total;
                $planet->save();
                $this->notify($planet->player,"Recurso recolhido, energia creditada", "Market");
                return true;
            } else {
                #Notificar, devolver energia para o comprador, cancelar a trading
                $this->notify($planet->player, "Quantidade de recurso insuficiente", "Market");
                Log::info("Notificar e cancelar por falta de recurso");
                return false;
            }
        }else{
            Log::info("erro no type?");
        }
    }
    /**
     * Finaliza a transação de compra, o comprador recebe o recurso
     * A transação não pode ter sido cancelada
     */
    private function finalizaCompra(Travel $travel){
        Log::info("finaliza a transação de compra, comprador recebe o recurso");
        $trading = Trading::find($travel->trading);
        #sincronizar recursos
        if($trading->type == 'S' && $trading->status != Trading::STATUS_CANCELED){
            $planet = Planet::find($travel->from);
            $keyResource = strtolower($trading->resource);
            $planet->{$keyResource} += $trading->quantity;
            $planet->save();
            $this->notify($travel->player, "Recurso recebido", "Market");
        }
    }
    /**
     * Realiza o recebimento do cargueiro com o recurso
     */
    public function realizaChegada(TravelService $travelService, Travel $travel)
    {
        try {
            Log::info("Devolvendo cargueiro, entregando recurso comprador- fim");
            $player = Player::find($travel->player);
            $player->transportShips += $travel->transportShips;
            $player->save();
            $this->finalizaCompra($travel);
            $travel->status = Travel::STATUS_FINISHED;
            $travel->save();
        } catch (Exception $e) {
            Log::info("TradingService - realizaChegada " . $e->getMessage());
        }
    }
    /**
     * @todo alterar o currency quando tiver tratando com tritium
     */
    private function atualizaStatusTrading(Trading $trading, $planetaInteressado)
    {
        $trade = Trading::find($trading->id);
        $trade->idPlanetInterested = $planetaInteressado;
        $trade->status = config("app.tritium_market_status_pending");
        $trade->currency = 'energy'; //default
        $trade->updatedAt = (new DateTime())->format('Y-m-d H:i:s');
        return $trade->save();
    }

    private function saveSafe($request, $idMarket, $planetCreator, $distancia, $transportShips = 1)
    {
        try {
            //ida e volta
            $travelTime = (config("app.tritium_travel_speed") * $distancia) * 2;
            $safe = new Safe();
            $safe->idPlanetSale = $request->idPlanetSale;
            $safe->idPlanetPurch = $request->idPlanetPurch;
            $safe->idPlanetCreator = $planetCreator;
            $safe->status = config("app.tritium_market_status_pending");
            $safe->deliveryTime = $travelTime;
            $safe->type = $request->type;
            $safe->currency = $request->currency;
            $safe->resource = strtolower($request->resource);
            $safe->quantity = $request->quantity;
            $safe->price = $request->price;
            $safe->total = $request->price * $request->quantity;
            $safe->idMarket = $idMarket;
            $safe->idTrading = $request->idTrading;
            $safe->transportShips = $transportShips;
            $safe->distance = $distancia;
            $safe->loaded = $request->type == 'P'; //nessa caso o cargueiro sai carregado
            $safe->save();
            return $safe->save();
        } catch (Exception $e) {
            return $e;
        }
    }
    public function getLastTrading()
    {
        try {
            $tradingFinish = new TradingFinished();
            $resultados = $tradingFinish->getLastTrading();
            return response()->json($resultados, Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['message' => 'erro ao recuperar ultima transacao ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        /*$resources = ['crystal', 'metal', 'uranium','teste'];
        $arr = [];
        foreach ($resources as $resource) {
            foreach ($resultados as $result) {
                if ($result->resource === $resource) {
                    $arr[$resource] = $result;
                    break;
                }
                else
                  $arr[$resource] = '';
            }
        }*/
        return response($resultados, Response::HTTP_OK);
    }

    public function verificaAndamentoSafe()
    {
        $safe = new Safe();
        $dadosSafe = $safe->getDadosSafe();
        $filtrado = $this->getDeliveryTimeConclued($dadosSafe);
        $atualizar = $this->atualizaTradingMetadeTempoConcluido($filtrado['metadeTempo']);
        $executados = $this->updateResourceTradeConclued($filtrado['concluido']);
        $parcial = $this->updateTradeParcial($filtrado['conclusaoParcial']);
        $saveFinish = $this->saveTradeFinish($filtrado['concluido']);
        $delete = $this->deleteTradingConcluidos($filtrado['concluido']);

        return response([
            'message' => 'Finish', 'success' => true,
            'info' => $dadosSafe,
            'filter' => $filtrado,
            'atualizarqe' => $atualizar,
            'executados' => $executados,
            'conclusaoParcial' => $parcial,
            'saveFinish' => $saveFinish,
            'delete' => $delete
        ], Response::HTTP_OK);
    }

    /**
     * Nessa situação, entrará os dados onde o tempo de entrega foi rapido e não deu tempo de executar a rotina de atualização, ou seja,
     * no intervalo de uma rotina e outra, o tempo da transação foi concluído porém não foi debitado/creditado os recursos do meio da transação
     * para ambos os planetas
     */
    private function updateTradeParcial($parcial)
    {
        if ($parcial) {
            foreach ($parcial as $p) {
                $p->concluido = false;
                $this->atualizaTradingMetadeTempoConcluido(array($p));
                $this->updateResourceTradeConclued(array($p));
            }
        }
        return "executou o parcial";
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
        $status = '';
        if ($concluidos) {
            foreach ($concluidos as $conc) {
                if ($conc->step === 'F') continue;
                $status = $this->debitarSaldosPlaneta($conc, 'fim');
                if (!$status) return false;
                DB::table('safe')->where('id', $conc->safeId)->update([
                    'step' => DB::raw("'F'")
                ]);
            }
        }
        return 'atualizou a situação da trade';
    }
    /**
     * atualiza as informações quando a metade do tempo de entrega se passou
     * caso o ativo esteja buscando e a metade passou, o cargueiro vai ficar carregado e deve-se debitar e creditar os valores
     * caso o ativo esteja entregando, o cargueiro deve ficar vazio.
     * caso o ativo vá entregar, com metade do tempo deve-se concluir a transação e a outra metade o cargueiro deve retornar vazio
     * retornando o array apenas para fins visual
     */
    private function atualizaTradingMetadeTempoConcluido($trades)
    {
        //Verificando quando a transação chegou na metade do tempo, quando for o ativo buscando, o cargueiro fica carregado e debita sua energia e irá retorna para sua origem
        $atualizados = [];
        try {
            foreach ($trades as $t) {
                $safe = Safe::find($t->safeId);
                // return $safe;
                if (!$safe || $t->concluido || $safe->step == 'M') continue;
                $safe->loaded = !$safe->loaded;
                $safe->step = 'M';
                $success = $this->debitarSaldosPlaneta($safe, 'meio');
                $atualizados[] = $success;
                if (!$success) continue;
                $successUpdate = $safe->save();
                return "atualizou aqui";
                // $atualizados[] = $safe;
            }
        } catch (Exception $e) {
            return response(["message" => "error in the middle of the transaction" . $e->getMessage(), "code" => 4010], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return "atualizou depois metade tempo concluido";
    }
    private function getDeliveryTimeConclued($dadosSafe)
    {
        $concluidos = [];
        $naoConcluidos = [];
        $metadeTempo = [];
        $conclusaoParcial = [];
        if ($dadosSafe) {
            foreach ($dadosSafe as $dados) {
                if ($dados->concluido && $dados->atingiuMetadeTempo && $dados->step == 'M')
                    $concluidos[] = $dados;
                if ($dados->concluido && $dados->atingiuMetadeTempo && $dados->step != 'M')
                    $conclusaoParcial[] = $dados;
                if (!$dados->concluido && !$dados->atingiuMetadeTempo)
                    $naoConcluidos[] = $dados;
                if (!$dados->concluido && $dados->atingiuMetadeTempo)
                    $metadeTempo[] = $dados;
            }
        }
        return ['concluido' => $concluidos, 'naoConcluido' => $naoConcluidos, 'metadeTempo' => $metadeTempo, 'conclusaoParcial' => $conclusaoParcial];
    }
    /**
     * @todo colocar o calculo de distancia
     */
    private function saveTradeFinish($concluidos)
    {
        // return ['aki'=>$concluidos];
        $s_status = [];
        try {
            foreach ($concluidos as $c) {
                // if ($c->step != 'F') continue;
                $finished = new TradingFinished();
                $finished->createdAt = $c->createdAt;
                $finished->idPlanetCreator = $c->idPlanetCreator;
                $finished->idPlanetInterested = $c->idPlanetInterested;
                $finished->quantity = $c->quantity;
                $finished->price = $c->price;
                $finished->distance = $c->distance;
                $finished->deliveryTime = $c->deliveryTime;
                $finished->idTrading = $c->idTrading;
                $finished->status = config("app.tritium_market_status_finished"); //concluido
                $finished->currency = $c->currency;
                $finished->type = $c->type;
                $finished->idMarket = $c->idMarket;
                $finished->resource = $c->resource;
                $finished->transportShips = $c->transportShips;
                $finished->finishedAt = $c->tempoFinal;
                $status = $finished->save();
                $s_status[] = $status;
                $this->notify($c->idPlanetCreator, 'CREADOR - FINAL', 'MARKET');
                $this->notify($c->idPlanetInterested, 'INTERESSADO - FINAL', 'MARKET');

                if (!$status) continue;
            }
        } catch (Exception $e) {
            return response(["message" => "error finished trading" . $e->getMessage(), "code" => 4010], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return ['akis' => $s_status, 'dados' => $concluidos];
    }
    private function deleteTradingConcluidos($concluidos)
    {
        if ($concluidos) {
            foreach ($concluidos as $concluido) {
                $trading = Trading::find($concluido->idTrading);
                $safe = Safe::where('idTrading', $concluido->idTrading)->first();
                if ($safe)
                    $safe->delete();
                if ($trading) {
                    $trading->status = 2;
                    $trading->save();
                }
            }
        }
        return "deletou trade e safe concluidos";
    }
    private function debitarSaldosPlanetaInicio($request, $etapa)
    {
        try {
            if ($request->type === 'S' && $etapa === 'inicio') {
                $planetaAtivo = Planet::find($request->idPlanetPurch);
                $planetaAtivo->energy -= ($request->quantity * $request->price);
                $planetaAtivo->save();

                $player = Player::findOrFail($planetaAtivo->player);
                $player->transportShips -= 1;
                $player->save();

                return "Debitando o inicio, ativo comprando";
            } elseif ($request->type === 'P' && $etapa === 'inicio') {
                $keyResource = strtolower($request->resource);
                $planetaAtivo = Planet::find($request->idPlanetSale);
                $planetaAtivo->{$keyResource} -= $request->quantity;
                $planetaAtivo->save();

                $player = Player::findOrFail($planetaAtivo->player);
                $player->transportShips -= 1;
                $player->save();

                return "Debitando o inicio, ativo vendendo";
            }
            return true; // Corrigir o que está retornando se necessário
        } catch (Exception $e) {
            return false;
        }
    }
    private function debitarSaldosPlanetaMeio($request, $etapa)
    {
        try {
            $keyResource = strtolower($request->resource);
            if ($request->type === 'S' && $etapa === 'meio') {
                $planetaPassivo = Planet::find($request->idPlanetSale);
                $planetaPassivo->energy += ($request->quantity * $request->price);
                $planetaPassivo->{$keyResource} -= $request->quantity;
                $planetaPassivo->save();
                $this->notify($planetaPassivo->id, 'Debitando o meio, ativo comprando', 'Market');
                return "Debitando o meio, ativo comprando";
            }
            if ($request->type === 'P' && $etapa === 'meio') {
                $planetaPassivo = Planet::find($request->idPlanetPurch);
                $planetaPassivo->{$keyResource} += $request->quantity;
                $planetaPassivo->energy -= ($request->quantity * $request->price);
                $planetaPassivo->save();
                $this->notify($planetaPassivo->id, 'Debitando o inicio, ativo vendendo', 'Market');
                return "Debitando o inicio, ativo vendendo";
            }

            // return true; // Corrigir o que está retornando se necessário
        } catch (Exception $e) {
            return false;
        }
    }
    private function debitarSadosPlanetaFim($request, $etapa)
    {
        if ($request->type === 'P' && $etapa === 'fim') {
            $planetaAtivo = Planet::find($request->idPlanetSale);
            $planetaAtivo->energy += ($request->quantity * $request->price);;
            $planetaAtivo->save();

            $player = Player::findOrFail($planetaAtivo->player);
            $player->transportShips  += 1;
            $player->save();

            $this->notify($planetaAtivo->id, 'Venda concluida, seu cargueiro retornou', 'Market');
            return "Debitando o fim, ativo vendendo";
        }
        if ($request->type === 'S' && $etapa === 'fim') {
            $keyResource = strtolower($request->resource);
            $planetaAtivo = Planet::find($request->idPlanetPurch);
            $planetaAtivo->{$keyResource} += $request->quantity;
            $planetaAtivo->save();

            $player = Player::findOrFail($planetaAtivo->player);
            $player->transportShips  += 1;
            $player->save();


            $this->notify($planetaAtivo->id, 'Compora concluida, seu cargueiro retornou', 'Market');
            return "Debitando o fim, ativo comprando";
        }
    }

    private function debitarSaldosPlaneta($request, $etapa)
    {
        if ($etapa === 'inicio')
            return $this->debitarSaldosPlanetaInicio($request, $etapa);
        if ($etapa === 'meio')
            return $this->debitarSaldosPlanetaMeio($request, $etapa);
        if ($etapa === 'fim')
            return $this->debitarSadosPlanetaFim($request, $etapa);
    }

    private function notify($playerId, $text, $type)
    {
        $log = new Logbook();
        $log->player = $playerId;
        $log->text = $text;
        $log->type = $type;
        $log->save();
    }
}
