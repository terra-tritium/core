<?php

namespace App\Services;

use App\Models\Aliance;
use App\Models\AlianceMember;
use App\Models\Player;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

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

    public function founderAliance(Request $request)
    {
        try {
            $player = Player::getPlayerLogged();
            $aliances = new Aliance();
            $aliances->name = $request->input('name');
            $aliances->description = $request->input('description');
            $aliances->avatar = $request->input('logo');
            $aliances->founder = $player->id;
            $success = $aliances->save();
            if (!$success)
                throw new \Exception('Erro ao salvar a aliança');
            $successMember = $this->createNewAlianceFounder($player->id, $aliances->id,"founder");
            if (!$successMember)
                throw new \Exception('Erro ao criar o fundador da aliança');
            //atualiza a alianca na tabela de usuarios
            DB::table('players')->where('id', $player->id)->update([
                'aliance' => DB::raw("$aliances->id")
            ]);
        } catch (Throwable $exception) {
            Log::error($exception);
            return response(['message' => 'Erro ao criar aliança ', 'code' => 4001, 'success' => false], Response::HTTP_BAD_REQUEST);
        }
    }
    public function createNewAlianceFounder($playerId, $alianceId, $role = "member")
    {
        $alianceMember = new AlianceMember();
        $alianceMember->player_id = $playerId;
        $alianceMember->idAliance = $alianceId;
        $alianceMember->role = $role;
        $alianceMember->dateAdmission = (new DateTime())->format('Y-m-d H:i:s');
        return $alianceMember->save();
    }
    public function getMembersAliance($alianceId){
        return (new AlianceMember())->getMembers($alianceId);
    }
}
