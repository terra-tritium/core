<?php

namespace App\Http\Controllers;

use App\Models\Effect;
use App\Models\NFTEffect;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class NFTEffectsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNftEffects(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $nftEffects = NFTEffect::paginate($perPage);
        return response()->json($nftEffects);
    }

    private function applyEffect($nftId)
    {
        try {
            $nft = NFTEffect::findOrFail($nftId);
        } catch (\Throwable $e) {
            Log::error('NFT not found' . $e->getMessage(), ['nftId' => $nftId]);
        }

        $effect = new Effect();

        switch ($nft->type) {
            case 1:
                $this->applyColonizerEffects($effect, $nft->rarity);
                break;
            case 2:
                $this->applyAssetEffects($effect, $nft->rarity);
                break;
            case 3:
                $this->applyKeyEffects($effect, $nft->rarity);
                break;
        }
        $effect->save();
    }


    private function applyColonizerEffects(Effect $effect, int $rarity)
    {
        switch ($rarity) {
            case 2:
                $effect->speedTravel += 5;
                break;
            case 3:
                $effect->speedTravel += 8;
                break;
        }
    }

    private function applyAssetEffects(Effect $effect, int $rarity)
    {
        if ($rarity === 4) {
            // Aplica efeitos para Asset Epic.
        } elseif ($rarity === 5) {
            // Aplica efeitos para Asset Legendary.
        }
    }

    private function applyKeyEffects(Effect $effect, int $rarity)
    {
        if ($rarity === 5) {
            // Aplica efeitos para Key Legendary.
        }
    }
}
