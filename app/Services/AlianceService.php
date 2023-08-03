<?php

namespace App\Services;

use App\Models\Aliance;
use App\Models\AlianceMember;
use App\Models\Building;
use App\Models\Logbook;
use App\Models\Player;
use DateTime;
use Exception;
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
            $aliances->logo = $request->input('logo');
            $aliances->founder = $player->id;
            $success = $aliances->save();
            if (!$success)
                throw new \Exception('Erro ao salvar a aliança');
            $successMember = $this->createNewAlianceFounder($player->id, $aliances->id, "founder");
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
    public function getMembersAliance($alianceId)
    {
        return (new AlianceMember())->getMembers($alianceId);
    }
    public function getMembersPending($alianceId){
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
        $responseData['open'] = 'alterar form';
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
            return response()->json(['message' => "erro ao deletar aliança" .$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'Alliances deleted successfully'], Response::HTTP_OK);
    }
    public function updateRequestMember($memberRequestId, $typeAction ){
        $alianceMember = AlianceMember::find($memberRequestId);
        $player = Player::find($alianceMember->player_id);
        if(!$alianceMember || !$player){
            return response()->json(['message' => 'Member request not found.'], Response::HTTP_NOT_FOUND);
        }
        if($player->aliance){
            return response()->json(['message' => "The player is already part of an alliance."], Response::HTTP_NOT_ACCEPTABLE);
        }
        if(!$this->availableSlot($alianceMember->idAliance)){
            return response()->json(['message' => "No slots available."], Response::HTTP_NOT_ACCEPTABLE);
        }
        if($typeAction == 'A'){
            $alianceMember->status = $typeAction;
            $alianceMember->dateAdmission = (new DateTime())->format('Y-m-d H:i:s');
            $alianceMember->save();
            $player->aliance = $alianceMember->idAliance;
            $player->save();
            $this->notify($player->id, "You have been accepted.","aliance");
        }
        if($typeAction == 'R'){
            $alianceMember->delete();
            $this->notify($player->id, "You have not been accepted","aliance");
        }
        return response()->json([],Response::HTTP_ACCEPTED);
    }
    private function notify($playerId, $text, $type)
    {
        $log = new Logbook();
        $log->player = $playerId;
        $log->text = $text;
        $log->type = $type;
        $log->save();
    }

    /**
     * @todo recuperar a quantide supotada para a aliança
     */
    private function getSupportedMemberCount($idAliance){
        return 10 * 1;
    }
    private function availableSlot($idAliance){
        $countMembers = AlianceMember::where([['idAliance','=',$idAliance],['status','=','A']])->count();
        $supported = $this->getSupportedMemberCount($idAliance);
        return $countMembers < $supported;
    }
}
