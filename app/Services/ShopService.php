<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Models\Redeem;
use App\Models\Player;
use App\Models\Planet;
use GuzzleHttp\Client;

class ShopService
{

  private $collection1k = 151;
  private $collection5k = 152;
  private $collection25k = 153;
  private $contract = "terra18sj5hluytww5hmy4teqsr4a7qr66pcd2x3qm9ns96ucpkdyserpszlx3qw";

  public function redeem($wallet, $collection, $token_id)
  {
    try {
      $player = Player::getPlayerLogged();
      $playerDTO = Player::find($player->id);

      $existsRedeem = Redeem::where([['contract', $this->contract], ['colection', $collection], ['token_id', $token_id]])->first();

      if ($existsRedeem) {
        return "Redeem no elegible";
      }

      if (!$this->validNFT($wallet, $collection, $token_id)) {
        return "Invalid NFT";
      }

      switch ($collection) {
        case $this->collection1k : $playerDTO->tritium += 1000;
          break;
        case $this->collection5k : $playerDTO->tritium += 5000;
          break;
        case $this->collection25k : $playerDTO->tritium += 25000;
          break;
      }

      $playerDTO->save();

      $reward = new Redeem();
      $reward->token_id = $token_id;
      $reward->user = $player->id;
      $reward->used = 1;
      $reward->contract = $this->contract;
      $reward->colection = $collection;
      $reward->save();

      return "ok";

    } catch (Exception $e) {
      Log::error('Erro ao executar um redeem nft: ' . $e->getMessage());
      return response()->json(
          ['message' => "Erro ao executar um redeem nft", 'error' => $e->getMessage()],
          Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }

  public function buy($code, $planetId) {

    $existsPlanet = Planet::find($planetId);

    $price = $this->getPrice($code);

    if (!$existsPlanet) {
      return "Invalid Planet";
    }

    if (!$this->hasFunds($price)) {
      return "No funds available for this item, cost: " . $price;
    }

    try {

      $this->processBuy($code, $planetId, $price);

      return "ok";

    } catch (Exception $e) {
      Log::error('Erro ao executar um process Buy: ' . $e->getMessage());
      return response()->json(
          ['message' => "Erro ao executar um process Buy", 'error' => $e->getMessage()],
          Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }

  public function used($wallet) {
    $client = new Client();

    $listCards = [];
    
    try {
        $moreNfts = true;
        $navigateIndex = 0;

        while ($moreNfts) {

          $query = base64_encode('{"tokens_with_info": { "owner": "'.$wallet.'", "start_after": '.$navigateIndex.'}}');

          $response = $client->request('GET', 'https://lcd-terraclassic.tfl.foundation/cosmwasm/wasm/v1/contract/'.$this->contract.'/smart/' . $query, [
              'headers' => [
                  'Accept' => 'application/json',
              ]
          ]);

          $body = $response->getBody();
          $data = json_decode($body, true);

          if(!isset($data['data']['tokens'])){
            return false;
          }

          if (count($data['data']['tokens']) <= 0) {
            return false;
          }

          foreach($data['data']['tokens'] as $token){

            $collection = $token['collection'];

            if ($collection == $this->collection1k ||
                $collection == $this->collection5k ||
                $collection == $this->collection25k) {

              $redeemFound = Redeem::where([['contract', $this->contract], ['colection', $collection], ['token_id', $token['token_id']]])->first();

              if ($redeemFound) {
                $token['used'] = 1;
              } else {
                $token['used'] = 0;
                array_push($listCards, $token);
              }
            }
          }

          if (count($data['data']['tokens']) < 20) {
            $moreNfts = false;
          } else {
            $navigateIndex += 20;
          }
        }

        return $listCards;

    } catch (\GuzzleHttp\Exception\GuzzleException $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }

  }

  private function getPrice($code) {
    switch ($code) {
      case "M001" : return 1000;
        break;
      case "M002" : return 3000;
        break;
      case "M003" : return 7000;
        break;
      case "U001" : return 2000;
        break;
      case "U002" : return 6000;
        break;
      case "U003" : return 14000;
        break;
      case "C001" : return 3000;
        break;
      case "C002" : return 9000;
        break;
      case "C003" : return 21000;
        break;
      case "E001" : return 2000;
        break;
      case "E002" : return 10000;
        break;
      case "H001" : return 5000;
        break;
    }
  }

  private function hasFunds($value) {
    $player = Player::getPlayerLogged();
    
    if ($player->tritium >= $value) {
      return true;
    }

    return false;
  }

  private function processBuy($code, $planetId, $price) {
    $player = Player::getPlayerLogged();

    $playerDTO = Player::find($player->id);

    switch ($code) {
      case "M001" : $this->addMetal($planetId, 25000);
        break;
      case "M002" : $this->addMetal($planetId, 100000);
        break;
      case "M003" : $this->addMetal($planetId, 250000);
        break;
      case "U001" : $this->addUranium($planetId, 25000);
        break;
      case "U002" : $this->addUranium($planetId, 100000);
        break;
      case "U003" : $this->addUranium($planetId, 250000);
        break;
      case "C001" : $this->addCrystal($planetId, 25000);
        break;
      case "C002" : $this->addCrystal($planetId, 100000);
        break;
      case "C003" : $this->addCrystal($planetId, 250000);
        break;
      case "E001" : $this->addEnergy($planetId, 50000);
        break;
      case "E002" : $this->addEnergy($planetId, 300000);
        break;
      case "H001" : $this->addHumanoid($planetId, 5);
        break;
    }

    $playerDTO->tritium -= $price;
    $playerDTO->save();
  }

  public function validNFT($wallet, $collection, $tokenId) {

    $client = new Client();
    $query = base64_encode('{"tokens_with_info": { "owner": "'.$wallet.'"}}');
    
    try {
        $moreNfts = true;

        while ($moreNfts) {
          $response = $client->request('GET', 'https://lcd-terraclassic.tfl.foundation/cosmwasm/wasm/v1/contract/terra18sj5hluytww5hmy4teqsr4a7qr66pcd2x3qm9ns96ucpkdyserpszlx3qw/smart/' . $query, [
              'headers' => [
                  'Accept' => 'application/json',
              ]
          ]);

          $body = $response->getBody();
          $data = json_decode($body, true);

          if(!isset($data['data']['tokens'])){
            return false;
          }

          if (count($data['data']['tokens']) <= 0) {
            return false;
          }

          foreach($data['data']['tokens'] as $token){
            if ($token['collection'] == $collection && $token['token_id'] == $tokenId) {
              return true;
            }
          }

          if (count($data['data']['tokens']) < 20) {
            $moreNfts = false;
          }
        }

    } catch (\GuzzleHttp\Exception\GuzzleException $e) {
        return response()->json(['error' => $e->getMessage()], 400);
    }

    return false;
  }

  private function addMetal($playerId, $qtd) {
    $planet = Planet::find($playerId);
    $planet->metal += $qtd;
    $planet->save();
  }

  private function addUranium($playerId, $qtd) {
    $planet = Planet::find($playerId);
    $planet->uranium += $qtd;
    $planet->save();
  }

  private function addCrystal($playerId, $qtd) {
    $planet = Planet::find($playerId);
    $planet->crystal += $qtd;
    $planet->save();
  }

  private function addEnergy($playerId, $qtd) {
    $planet = Planet::find($playerId);
    $planet->energy += $qtd;
    $planet->save();
  }

  private function addHumanoid($playerId, $qtd) {
    $planet = Planet::find($playerId);
    $planet->workers += $qtd;
    $planet->workersWaiting += $qtd;
    $planet->save();
  }
  
}
