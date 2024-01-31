<?php

namespace App\Http\Controllers;

use App\Models\NFTConfig;
use App\Models\Player;
use App\Services\NFTConfigService;
use Illuminate\Http\Response;
use Exception;

class NFTController extends Controller
{
  public function __construct(protected readonly NFTConfigService $nftConfigService)
  {
  }

  public function config($slot, $code) {

    try{
        $player = $this->getPlayerLogged();

        $nftUserConfig = $this->nftConfigService->nftConfig($player);

        if ($slot >= 1 && $slot <= 5) {
            $nftUserConfig->{'slot' . $slot} = $code;
        } else {
            throw new Exception('Invalid slot provided.');
        }

        $nftUserConfig->save();

    } catch (Exception $e) {
        return response(["msg" => "error " . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    return response(['message' => 'success!', 'success' => true], Response::HTTP_OK);

  }

  public function get() {
      $player = $this->getPlayerLogged();
      $nftUserConfig = NFTConfig::where('player', $player->id)->first();
      return $nftUserConfig;
  }

    private function getPlayerLogged()
    {
        return Player::getPlayerLogged();
    }
}
