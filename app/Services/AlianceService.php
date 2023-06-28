<?php

namespace App\Services;

use App\Models\Player;
use Illuminate\Support\Facades\DB;

class AlianceService
{
    public function acceptPlayerRequest($playerId, $alianceId)
    {
        $request = DB::table('aliances_requests')
            ->where('player_id', $playerId)
            ->where('aliance_id', $alianceId)
            ->first();

        if (!$request) {
            return false;
        }

        Player::where('id', $playerId)->update(['aliance' => $alianceId]);

        DB::table('aliances_requests')
            ->where('player_id', $playerId)
            ->where('aliance_id', $alianceId)
            ->delete();

        return true;
    }

}
