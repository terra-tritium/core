<?php

namespace App\Services;

use App\Models\Aliance;
use App\Models\AlianceMember;
use App\Models\AlianceRequest;
use App\Models\Logbook;
use App\Models\Player;
use App\Models\RankMember;
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
            //
        } catch (Throwable $exception) {
            Log::error($exception);
            return response(['message' => 'Erro ao criar aliança ', 'code' => 400, 'err' => $exception, 'success' => false], Response::HTTP_BAD_REQUEST);
        }
    }
    public function createNewAlianceFounder($playerId, $alianceId, $role = "member")
    {
        $alianceMember = new AlianceMember();
        $alianceMember->player_id = $playerId;
        $alianceMember->idAliance = $alianceId;
        $alianceMember->role = $role;
        // $alianceMember->idRank = env("TRITIUM_MEMBER_FOUNDER");
        $alianceMember->idRank = 1;

        $alianceMember->dateAdmission = (new DateTime())->format('Y-m-d H:i:s');
        return $alianceMember->save();
    }
    public function joinAlliance($aliancaId, $acceptdInvite = false)
    {
        $aliance = Aliance::findOrFail($aliancaId);

        $player = Player::getPlayerLogged();
        $player->aliance = $aliancaId;
        if ($aliance->status === 'A' || $acceptdInvite) {
            if(!$this->availableSlot($aliancaId)){
                return response()->json(['message'=>"There are no vacancies available for this alliance."],Response::HTTP_FORBIDDEN);
            }
            //verificar se tem vaga, ingressar, avisar founder
            $success =  $this->saveRequest($player->id, $aliance->id, $acceptdInvite ? 'A' : $aliance->status);
            if ($success) {
                $this->notify($aliance->founder, "A new member has joined the alliance!", "aliance");
                DB::table('players')->where('id', $player->id)->update([
                    'aliance' => DB::raw($aliance->id)
                ]);
                $this->notify($player->id, "You are now a part of an alliance!", "aliance");
                if($acceptdInvite){
                    AlianceMember::where('player_id', $player->id)
                    ->where('status', '!=', 'A')
                    ->delete();   
                    //zerar todos os convites
                }
                AlianceRequest::where('player_id', $player->id)->delete();                 
            }
        } else {
            $this->notify($aliance->founder, "A member has requested to join the alliance!", "aliance");
            $this->notify($player->id, "Request sent for review!", "aliance");
            return $this->saveRequest($player->id, $aliance->id, $aliance->status);
        }
        return response()->json([], Response::HTTP_OK);
    }

    private function saveRequest($playerId, $alianceId, $status)
    {
        $alianceMember = new AlianceMember();
        $alianceMember->idAliance = $alianceId;
        $alianceMember->player_id = $playerId;
        $alianceMember->status = $status === 'F' ? 'P' : 'A';
        $alianceMember->role = 'member';
        $alianceMember->dateAdmission = $status === 'A' ? (new DateTime())->format('Y-m-d H:i:s') : null;
        return $alianceMember->save();
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
        try {
            $alianceMember = AlianceMember::where('player_id', $playerId)->first();
            $aliance = Aliance::find($alianceMember->idAliance ?? 0);
            // return response(['member'=>$alianceMember, "aliance"=>$aliance], Response::HTTP_OK);
            $rank = RankMember::find($alianceMember->idRank);
            if (!$alianceMember || !$aliance) {
                return response(['message' => 'Alliance not found.'], Response::HTTP_NOT_FOUND);
            }

            $responseData = $alianceMember;
            $responseData['currentPlayer'] = $playerId;
            $responseData['role'] = $rank->rankName;
            $responseData['logo'] = $aliance->logo;
            $responseData['countMembers'] = AlianceMember::where([['idAliance', '=', $alianceMember->idAliance], ['status', '=', 'A']])->count();
            return response($responseData, Response::HTTP_OK);
        } catch (Exception $e) {
            return response(["message" => $e->getMessage(), "teste" => 'teste'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        if (!$this->availableSlot($alianceMember->idAliance) && $typeAction == 'A') {
            return response()->json(['message' => "No slots available."], Response::HTTP_NOT_ACCEPTABLE);
        }
        if ($typeAction == 'A') {
            $alianceMember->status = $typeAction;
            $alianceMember->dateAdmission = (new DateTime())->format('Y-m-d H:i:s');
            $alianceMember->idRank = env("TRITIUM_MEMBER_SOLDIER");
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

    private function getSupportedMemberCount($idAliance)
    {
        $alianca = Aliance::findOrFail($idAliance);
        $aliance = new Aliance();
        $level = $aliance->getLevelBuildAliance($alianca->founder);
        return $level * env("TRITIUM_COUNT_MEMBER_LEVEL_ALIANCE");
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
            $alianca['totalMembers'] = $level * env("TRITIUM_COUNT_MEMBER_LEVEL_ALIANCE");
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
    public function getMembersRank($idAliance)
    {
        try {
            $alianceMember = new AlianceMember();
            $ranksMember = $alianceMember->getAlianceRanks($idAliance);
            return response()->json($ranksMember, Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json($e, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function changeRankMember($idRank, $idMember, $idAliance)
    {
        try {
            $alianceMember = AlianceMember::find($idMember);
            $alianceMem = new AlianceMember();
            $ranksMember = $alianceMem->getAlianceRanks($idAliance);
            $membroRebaixado = 'rebaixado';
            $promovido = "promovido";
            foreach ($ranksMember as $rank) {
                if ($rank->idRank == $idRank) {
                    if (!$rank->roleAvailable) {
                        $membroRebaixado = AlianceMember::where([['idAliance', '=', $idAliance], ['idRank', '=', $idRank]])->first();
                        $this->alterarPatenteMembro($membroRebaixado, env("TRITIUM_MEMBER_SOLDIER"));
                        /**Rebaixa */
                        $promovido = $alianceMember;
                        $this->alterarPatenteMembro($alianceMember, $idRank);
                        /**Promove */
                        break;
                    } else {
                        $this->alterarPatenteMembro($alianceMember, $idRank);
                    }
                } else {
                    /**Quando ainda não existe ninguem com esse cargo */
                    $this->alterarPatenteMembro($alianceMember, $idRank);
                }
            }
            // if (!$ranksMember) {
            //     $promovido = "Foi promovido pq nao tinha membro nesse cargo";
            //     $this->alterarPatenteMembro($alianceMember, $idRank);
            // }

            return response()->json(
                [
                    'Membro' => $alianceMember,
                    'rankId' => $idRank,
                    'ranks' => $ranksMember,
                    'rebaixado' => $membroRebaixado,
                    "promovido" => $promovido
                ],
                Response::HTTP_OK
            );
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao trocar patente']);
        }
    }
    private function alterarPatenteMembro($alianceMember, $idRank)
    {
        $alianceMember->idRank = $idRank;
        $alianceMember->save();
        $this->notify($alianceMember->player_id, "Your rank has been changed.", "aliance");
    }
    public function deixarCargo($idAliance, $idMember)
    {
        try {
            $alianceMember = AlianceMember::find($idMember);
            $aliance = Aliance::find($idAliance);
            $alianceMember->idRank = env("TRITIUM_MEMBER_SOLDIER");
            $alianceMember->save();
            $this->notify($alianceMember->player_id, "You relinquished your rank.", "aliance");
            $this->notify($aliance->founder, "A member relinquished their rank.", "aliance");
            return response()->json(['message' => 'Voce deixou seu cargo na aliança'], Response::HTTP_ACCEPTED);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao deixar patente'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function invite(Request $request)
    {
        try {
            $loggedPlayer = Player::getPlayerLogged();
            $idNewMember = $request->input('idPlayer');
            $idAliance = $request->input("idAliance");
            $aliance = Aliance::find($idAliance);
            if (!$aliance) {
                return response()->json(['message' => "Aliance does not exist."], Response::HTTP_NOT_FOUND);
            }
            $newMember = Player::find($idNewMember);
            if (!$newMember) {
                return response()->json(['message' => "Player does not exist."], Response::HTTP_NOT_FOUND);
            }
            if ($newMember->aliance == $idAliance) {
                //tratar msg no front
                return response()->json(['message' => "The player is already a member of this alliance."], Response::HTTP_CONFLICT);
            }
            $conviteJaEnviado = AlianceRequest::where([['player_id', '=', $idNewMember], ["alianceId", "=", $idAliance]])->count();
            if ($conviteJaEnviado > 0) {
                //tratar msg no front
                return response()->json(['message' => "The player has already received an invitation from this alliance."], Response::HTTP_CONFLICT);
            }
            //verificar se o membro ja tem alianca
            //verificar o limite da alianca
            //verificar se o membro ja recebeu convite dessa alianca
            //salvar msg
            //avisar o jogador que enviou o convite
            //avisar o jogador que recebeu o convite
            //alterar o tipo de dado do status
            $alianceRequest = new AlianceRequest();
            $alianceRequest->player_id = $idNewMember;
            $alianceRequest->sentBy = $loggedPlayer->id;
            $alianceRequest->alianceId = $idAliance;
            $alianceRequest->message = "aceita?";
            $alianceRequest->created_at = now();
            $alianceRequest->status = 0;
            $alianceRequest->save();
            $this->notify($idNewMember, "You have received an invitation to join a new alliance", "aliance");
            $this->notify($loggedPlayer->id, "Invitation sent to: " . $newMember->name, "Aliance");
            // return ['idAliance' => $idAliance, "jogador" => $newMember, "aliance" => $aliance, 'conviteJaEnviado' => $conviteJaEnviado];
            return response()->json(['message' => 'fazer os ajustes'], Response::HTTP_ACCEPTED);
        } catch (Exception $e) {
            return response()->json(['message' => "erro " . $e->getMessage()], 400);
        }
    }

    public function getDataReceivedInvitationAliance( $player)
    {
        $alianceRequest = AlianceRequest::where('player_id', $player->id)->get();
        foreach ($alianceRequest as $key => $value) {
            $aInveited = Aliance::where('id', $value->alianceId)->first();
            $aliance = new Aliance();
            $level = $aliance->getLevelBuildAliance($aInveited->founder);
            $aInveited['level'] = $level;
            $aInveited['totalMembers'] = $level * env("TRITIUM_COUNT_MEMBER_LEVEL_ALIANCE"); 
            $qtdMembrosAtivos = AlianceMember::where([['idAliance','=',$aInveited->id],['status','=','A']])->count();
            $aInveited['members'] = $qtdMembrosAtivos;
            $alianceRequest[$key]['aliance'] = $aInveited;
            $alianceRequest[$key]['totalMembers'] = $level * env("TRITIUM_COUNT_MEMBER_LEVEL_ALIANCE");
            $alianceRequest[$key]['members'] = $qtdMembrosAtivos;
        }
        return $alianceRequest;
    }
}
