<?php

namespace App\Services;

use App\Models\Aliance;
use App\Models\ChatAliance;
use App\Models\Player;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ChatAlianceService
{
  protected $chatAliance;

  public function __construct()
  {
    $this->chatAliance = new ChatAliance();
  }

  public function newMessageAlianceChat(Request $request)
  {
    try {
      $player = Player::getPlayerLogged();
      $aliancaOrigem = Aliance::find($request->input('idOrigem'));
      $aliancaDestino = Aliance::find($request->input('idDestino'));
      if (!$aliancaDestino || !$aliancaOrigem) {
        return response()->json(["message" => "destination or source alliance not found."], Response::HTTP_NOT_FOUND);
      }
      $this->chatAliance->idOrigem = $aliancaOrigem->id;
      $this->chatAliance->idDestino = $aliancaDestino->id;
      $this->chatAliance->message = $request->input('message');
      $this->chatAliance->player = $player->id;
      $this->chatAliance->save();
      return response()->json($this->chatAliance, Response::HTTP_OK);
    } catch (Exception $e) {
      Log::error('error while sending a message to another alliance' . $e->getMessage());
      return response()->json(["message" => "error while sending a message to another alliance."], Response::HTTP_INTERNAL_SERVER_ERROR);

    }
  }

  public function getMessageWithAliance($destino){
    try{
      $conversation = $this->chatAliance->getMessageAliance($destino);
      
      return response()->json($conversation,Response::HTTP_OK);
    }catch(Exception $e){
      Log::error('error when fetching messages' . $e->getMessage());
      return response()->json(["message" => "error when fetching messages.", "erro"=>$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);

    }

  }
}
