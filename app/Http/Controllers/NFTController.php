<?php

namespace App\Http\Controllers;

use App\Models\NFTConfig;
use App\Models\Player;
use App\Services\NFTConfigService;
use Illuminate\Http\Response;
use Exception;

class NFTController extends Controller
{
  public function __construct(protected readonly NFTConfigService $nFTConfigService)
  {
  }
 
  public function config($slot, $code) {
    $player = Player::getPlayerLogged();

    try{ 
      $nftUserConfig = $this->nFTConfigService->nftConfig($player);
      switch ($slot) {
        case 1:
          $nftUserConfig->slot1 = $code;
          break;
        case 2:
          $nftUserConfig->slot2 = $code;
          break;
        case 3:
          $nftUserConfig->slot3 = $code;
          break;
        case 4:
          $nftUserConfig->slot4 = $code;
          break;
        case 5:
          $nftUserConfig->slot5 = $code;
          break;
      }

      $nftUserConfig->save();

    } catch (Exception $e) {
        return response(["msg" => "error " . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
    return response(['message' => 'success!', 'success' => true], Response::HTTP_OK);

  }

  public function get() {
    $player = Player::getPlayerLogged();
    $nftUserConfig = NFTConfig::where('player', $player->id)->first();
    return $nftUserConfig;
  }
}
