<?php

namespace App\Services;

use App\Models\ChatGroup;
use App\Models\MessageGroup;
use App\Models\Player;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Log;
use Throwable;

class MessageService
{
public function getMessagesGroupAliance($idAliance)
    {
        try {
            $loggedPlayer = Player::getPlayerLogged();
            $messageGroup = new MessageGroup();
            $mensagens = $messageGroup->getMessagesGroupAliance($idAliance, $loggedPlayer->id);
            return response()->json($mensagens,Response::HTTP_OK);
        } catch (Exception $e) {
            Log::error('Erro ao recuperar mensagens em grupo: ' . $e->getMessage());
            return response()->json(
                ['message' => "Erro ao enviar messagem", 'error' =>$e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
    public function setMessageAlianceGroup(Request $request)
    {
        try {
            $dados = $request->input('messageChatAliance');
            $chatGroup = ChatGroup::where([['idAliance', $dados['idAliance']], ['status', true]])->first();
            if (!$chatGroup) {
                $chatGroup = $this->createNewChatGroup($dados['idAliance']);
            }
            if ($chatGroup) {
                $loggedPlayer = Player::getPlayerLogged();
                $messageGroup = new MessageGroup();
                $messageGroup->idChatGroup = $chatGroup->id;
                $messageGroup->remetenteId = $loggedPlayer->id;
                $messageGroup->message = $dados['message'];
                $messageGroup->status = true;
                $messageGroup->save();
            }
            return response()->json(
                [],
                Response::HTTP_CREATED
            );
        } catch (Exception $e) {
            Log::error('Erro ao criar novo grupo/message: ' . $e->getMessage());
            return response()->json(
                ['message' => "Erro ao enviar messagem"],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
    public function createNewChatGroup($idAliance)
    {
        try {
            $chatGroup = new ChatGroup();
            $chatGroup->idAliance = $idAliance;
            $chatGroup->groupName = "Group Aliance";
            $chatGroup->status = true;
            $chatGroup->save();
            return $chatGroup;
        } catch (Exception $e) {
            Log::error('Erro ao criar novo grupo: ' . $e->getMessage());
            return false;
        }
        return false;
    }
    public function deleteMessage($idMessage){
        try{
            $msg = MessageGroup::find($idMessage);
            if($msg){
                $msg->status = false;
                $msg->save();
            }
            return response()->json(['message'=>"atualizado"],Response::HTTP_ACCEPTED);
        }catch(Exception $e){
            Log::error('Erro ao apagar a mensagem: ' . $e->getMessage());
            return response()->json(['message'=>"erro ao deletar menesagem"], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
