<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Services\TritiumService;
use GuzzleHttp\Client;

class TritiumController extends Controller
{
    private $lcd = "https://terra-classic-fcd.publicnode.com";
    private $contract = "terra1g6fm3yu79gv0rc8067n2nnfpf0vks6n0wpzaf4u7w48tdrmj98zsy7uu00";

    public function upgrade($planetId, $building){
        try {
            $tritiumService = new TritiumService();
            $response = $tritiumService->upgrade($planetId, $building);
            if ($response == "ok") {
                return response()->json(['message' => $response], Response::HTTP_OK);
            }
            return response()->json(['message' => $response], Response::HTTP_BAD_REQUEST);
            
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Tritium miner controller error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function claim($building){
        try {
            $tritiumService = new TritiumService();
            $response = $tritiumService->claim($building);
            if ($response == "ok") {
                return response()->json(['message' => $response], Response::HTTP_OK);
            }
            return response()->json(['message' => $response], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Tritium miner controller error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function swap($txhash) {
        $maxRetries = 5;
        $attempt = 0;

        $client = new Client();

        while ($attempt < $maxRetries) {
            try {
                $response = $client->request('GET', $this->lcd . "/v1/tx/" . $txhash);
                
                if ($response->getStatusCode() == 200) {
                    $transactionDetails = json_decode($response->getBody()->getContents(), true);
                    
                    // Verifique se as chaves existem para garantir o processo de burn
                    if (isset($transactionDetails['tx']['value']['msg'][0]['value']['msg']['burn']['amount'])) {
                        $amount = $transactionDetails['tx']['value']['msg'][0]['value']['msg']['burn']['amount'];
                        $contract = $transactionDetails['tx']['value']['msg'][0]['value']['contract'];
                        $memo = $transactionDetails['tx']['value']['memo'];

                        if ($contract != $this->contract) {
                            return response()->json(['message' => "Invalid token contract"], Response::HTTP_BAD_REQUEST);
                        }

                        $tritiumService = new TritiumService();
                        $response = $tritiumService->swap($txhash, $amount, $memo);

                        if ($response == "ok") {
                            return response()->json(['message' => $response], Response::HTTP_OK);
                        }
                        return response()->json(['message' => $response], Response::HTTP_BAD_REQUEST);
                    } else {
                        // Tratamento para o caso de a chave não existir
                        throw new \Exception("Transaction details are incomplete.");
                    }

                    return response()->json($transactionDetails);
                } else {
                    throw new \Exception("Unexpected status code received: " . $response->getStatusCode());
                }
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                // Se o código de status for 404, tente novamente
                if ($e->getResponse()->getStatusCode() == 404) {
                    $attempt++;
                    if ($attempt >= $maxRetries) {
                        break;
                    }
                    sleep(6);  // Aguarda 6 segundos antes de tentar novamente
                } else {
                    // Se for um erro diferente de 404, lance a exceção
                    throw $e;
                }
            } catch (\Exception $e) {
                error_log($e->getMessage());
                return response()->json(['error' => 'Failed to retrieve transaction details: ' . $e->getMessage()], 400);
            }
        }
        return response()->json(['error' => 'Transaction not found after maximum retries'], 404);
    }
}
