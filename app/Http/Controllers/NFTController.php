<?php

namespace App\Http\Controllers;

use App\Models\NFTConfig;
use App\Models\Player;
use App\Services\NFTConfigService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Exception;

class NFTController extends Controller
{
      public function __construct(protected readonly NFTConfigService $nftConfigService)
      {
      }

    /**
     * @param int $slot
     * @param int $code
     * @return Application|ResponseFactory|\Illuminate\Foundation\Application|Response
     */
      public function config(int $slot, int $code) {

            try{
                $nftUserConfig = $this->nftConfigService->nftConfig($this->getPlayerLogged());

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
          try {
             return NFTConfig::where('player', $this->getPlayerLogged()->id)->first();
          } catch (Exception $e) {
              return response()->json(['error' => 'NFT configuration not found for the logged player'], Response::HTTP_NOT_FOUND);
          }
      }

      private function getPlayerLogged()
      {
            return Player::getPlayerLogged();
      }
}
