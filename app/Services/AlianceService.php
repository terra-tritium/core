<?php

namespace App\Services;

use App\Models\Aliance;
use App\Models\AlianceMember;
use App\Models\Building;
use App\Models\Logbook;
use App\Models\Planet;
use App\Models\Player;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Env;
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
            if ($request->input('id')) {
                $aliances = Aliance::find($request->id);
            } else {
                $aliances = new Aliance();
            }
            $aliances->name = $request->input('name');
            $aliances->description = $request->input('description');
            $aliances->logo = $request->input('logo');
            $aliances->founder = $player->id;
            $aliances->status = $request->input('status');
            $success = $aliances->save();
            if (!$success)
                throw new \Exception('Erro ao salvar a aliança');

            if (!$request->input('id')) {
                $successMember = $this->createNewAlianceFounder($player->id, $aliances->id, "founder");
                if (!$successMember)
                    throw new \Exception('Erro ao criar o fundador da aliança');
                //atualiza a alianca na tabela de usuarios
                DB::table('players')->where('id', $player->id)->update([
                    'aliance' => DB::raw("$aliances->id")
                ]);
            }
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
    public function joinAlliance($aliancaId)
    {
        $aliance = Aliance::findOrFail($aliancaId);

        $player = Player::getPlayerLogged();
        $player->aliance = $aliancaId;
        if ($aliance->status === 'A') {
            //verificar se tem vaga, ingressar, avisar founder
            $success =  $this->saveRequest($player->id, $aliance->id, $aliance->status);
            if ($success) {
                $this->notify($aliance->founder, "A new member has joined the alliance!", "aliance");
                DB::table('players')->where('id', $player->id)->update([
                    'aliance' => DB::raw($aliance->id)
                ]);
                $this->notify($player->id, "You are now a part of an alliance!", "aliance");
            }
        } else {
            $this->notify($aliance->founder, "A member has requested to join the alliance!", "aliance");
            $this->notify($player->id, "Request sent for review!", "aliance");
            return $this->saveRequest($player->id, $aliance->id, $aliance->status);
        }
        return response()->json(['alianca' => $aliance, 'player' => $player], Response::HTTP_OK);
    }
    //"SQLSTATE[HY000]: General error: 1364 Field 'idAliance' doesn't have a default value (Connection: mysql, SQL: insert into `aliances_members` (`player_id`, `status`, `role`, `dateAdmission`) values (39, A, member, ?))"

    private function saveRequest($playerId, $alianceId, $status)
    {
        $alianceMember = new AlianceMember();
        $alianceMember->idAliance = $alianceId;
        $alianceMember->player_id = $playerId;
        $alianceMember->status = $status === 'F' ? 'P' : 'A';
        $alianceMember->role = 'member';
        $alianceMember->dateAdmission = $status === 'A' ? (new DateTime())->format('Y-m-d H:i:s') : null;
        return $alianceMember->save();
        /*
          'player_id',
        'createdAt',
        'role',
        'status',
        'dateAdmission',
        'dateOf',
        'idAliance'
        */
    }
    public function getMembersAliance($alianceId)
    {
        return (new AlianceMember())->getMembers($alianceId);
    }
    public function getMembersPending($alianceId)
    {
        return (new AlianceMember())->getMembersPending($alianceId);
    }
    public function getDetailsMyAliance($playerId)
    {
        $alianceMember = AlianceMember::where('player_id', $playerId)->first();
        $aliance = Aliance::find($alianceMember->idAliance ?? 0);
        if (!$alianceMember || !$aliance) {
            return response()->json(['message' => 'Alliance not found.'], Response::HTTP_NOT_FOUND);
        }
        $responseData = $alianceMember;
        $responseData['logo'] = $aliance->logo;
        $responseData['countMembers'] = AlianceMember::where([['idAliance', '=', $alianceMember->idAliance], ['status', '=', 'A']])->count();
        return response()->json($responseData, Response::HTTP_OK);
    }
    public function removeMember($memberId)
    {
        $member = AlianceMember::find($memberId);
        $player = Player::find($member->player_id ?? 0);
        if (!$member || !$player) {
            return response()->json(['message' => 'Member not found.'], Response::HTTP_NOT_FOUND);
        }
        $member->status = 'R';
        $member->dateOf = (new DateTime())->format('Y-m-d H:i:s');
        $member->save();
        DB::table('players')->where('id', $player->id)->update([
            'aliance' => DB::raw("null")
        ]);

        return response()->json([], Response::HTTP_ACCEPTED);
    }
    public function deleteAliance($alianceId)
    {
        try {
            $aliances = Aliance::findOrFail($alianceId);
            $members = AlianceMember::where([['idAliance', '=', $alianceId]])->get();
            if ($members) {
                foreach ($members as $member) {
                    DB::table('players')->where('id', $member->player_id)->update([
                        'aliance' => DB::raw("null")
                    ]);
                    $this->notify($member->player_id, "Alliance deleted", "aliance");
                    $membro = AlianceMember::find($member->id);
                    $membro->delete();
                }
            }
            $aliances->delete();
        } catch (Exception $e) {
            Log::error('Erro no agendamento: ' . $e->getMessage());
            return response()->json(['message' => "erro ao deletar aliança" . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'Alliances deleted successfully'], Response::HTTP_OK);
    }
    public function updateRequestMember($memberRequestId, $typeAction)
    {
        $alianceMember = AlianceMember::find($memberRequestId);
        $player = Player::find($alianceMember->player_id);
        if (!$alianceMember || !$player) {
            return response()->json(['message' => 'Member request not found.'], Response::HTTP_NOT_FOUND);
        }
        if ($player->aliance && $typeAction == 'A') {
            return response()->json(['message' => "The player is already part of an alliance."], Response::HTTP_NOT_ACCEPTABLE);
        }
        if (!$this->availableSlot($alianceMember->idAliance)) {
            return response()->json(['message' => "No slots available."], Response::HTTP_NOT_ACCEPTABLE);
        }
        if ($typeAction == 'A') {
            $alianceMember->status = $typeAction;
            $alianceMember->dateAdmission = (new DateTime())->format('Y-m-d H:i:s');
            $alianceMember->save();
            $player->aliance = $alianceMember->idAliance;
            $player->save();
            $this->notify($player->id, "You have been accepted.", "aliance");
        }
        if ($typeAction == 'R') {
            $alianceMember->delete();
            DB::table('players')->where('id', $alianceMember->player_id)->update([
                'aliance' => DB::raw("null")
            ]);
            $this->notify($player->id, $alianceMember->status === 'P' ? "You have not been accepted" : "You have been removed from the alliance", "aliance");
        }
        return response()->json([], Response::HTTP_ACCEPTED);
    }
    private function notify($playerId, $text, $type)
    {
        $log = new Logbook();
        $log->player = $playerId;
        $log->text = $text;
        $log->type = $type;
        $log->save();
    }
    public function getAvailableName($name)
    {
        $findName = Aliance::where([['name', '=', $name]])->get();
        if (count($findName) > 0)
            return response()->json(['message' => "Name not available"], Response::HTTP_CONFLICT);
        else
            return response()->json($findName, Response::HTTP_OK);
    }
    /**
     * @todo recuperar a quantide supotada para a aliança
     */
    private function getSupportedMemberCount($idAliance)
    {
        return 10 * 1;
    }
    private function availableSlot($idAliance)
    {
        $countMembers = AlianceMember::where([['idAliance', '=', $idAliance], ['status', '=', 'A']])->count();
        $supported = $this->getSupportedMemberCount($idAliance);
        return $countMembers < $supported;
    }
    public function getLevelAliancesFounder($idFounder, $aliances)
    {
    }
    public function getDadosBuildsAliance($dadosAlianca)
    {
        $aliancas = [];
        foreach ($dadosAlianca as $dados) {
            $aliance = new Aliance();
            $level = $aliance->getLevelBuildAliance($dados->founder);
            $alianca = (array) $dados;
            $alianca['level'] = $level;
            $alianca['totalMembers'] = $level * env("COUNT_MEMBER_LEVEL_ALIANCE");
            $aliancas[] = $alianca;
        }
        return $aliancas;
    }
    public function exit($alianceId)
    {
        try {
            $player = Player::getPlayerLogged();
            $member = AlianceMember::where('player_id', '=', $player->id)->first();
            $aliance = Aliance::find($alianceId);
            DB::table('aliances_members')
                ->where('player_id', $player->id)
                ->delete();
            DB::table('players')->where('id', $player->id)->update([
                'aliance' => DB::raw("null")
            ]);
            $this->notify($player->id, "You left the alliance!", "aliance");
            $this->notify($aliance->founder, "A member left the alliance", "aliance");
            return response()->json([], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao deixar a aliança'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function cancelRequest()
    {
        try {
            $player = Player::getPlayerLogged();
            $memberRequest = AlianceMember::where('player_id', '=', $player->id)->first();
            $memberRequest->delete();
            return response()->json([], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao cancelar requisição'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
