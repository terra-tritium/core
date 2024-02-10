<?php

namespace App\Http\Controllers;

use App\Models\Planet;
use App\Models\Player;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Services\MessageService;
use DateTime;
use Exception;
use Illuminate\Http\Response;

class MessageController extends Controller
{

    public function __construct(protected readonly MessageService $messageService) 
    {
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Planet  $planet
     * @return \Illuminate\Http\Response
     */
    public function show(Planet $planet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Planet  $planet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Planet $planet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Planet  $planet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Planet $planet)
    {
        //
    }

    /**
     *
     * * @OA\Get(
     *     path="/list",
     *     summary="Obter lista de planetas do jogador",
     *     operationId="getPlanetList",
     *     tags={"Planetas"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de planetas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Planet")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado"
     *     )
     * )
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $player = Player::getPlayerLogged();

        if (!$player) {
            return response()->json(['error' => 'Unauthenticated player.'], Response::HTTP_UNAUTHORIZED);
        }

        $planets = Planet::where('player', $player->id)->get();

        return response()->json($planets);
    }

    public function ping()
    {
        $msg = app(Message::class);
        $dados = $msg->getMsg();
        return $dados;
    }

    /**
     * @OA\Get(
     *     path="/messages",
     *     summary="Obter todas as mensagens",
     *     operationId="getAllMessages",
     *     tags={"Mensagens"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de mensagens",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro interno do servidor"
     *     )
     * )
     * @return mixed
     */
    public function getAll()
    {
        $msg = app(Message::class);
        return $msg->getAll();
    }

    public function getAllByUserSender($id)
    {
        $player = Player::getPlayerLogged();
        $msg = app(Message::class);
        return $msg->getAllByUserSender($player->user);
    }

    public function getAllByUserRecipient()
    {
        $player = Player::getPlayerLogged();
        $msg = app(Message::class);
        return $msg->getAllByUserRecipient($player->user);
    }

    public function getCountMessageNotRead(){
        $naoLidas = $this->getAllMessageNotRead();
        return $naoLidas;
    }

    private function messageForSender($messages)
    {
        $trocas = array();

        foreach ($messages as $message) {
            $sender = $message->sender;

            if (!isset($trocas[$sender])) {
                $trocas[$sender] = array();
            }

            $trocas[$sender][] = $message;
        }
        return $trocas;
    }

    public function getAllMessageSenderForRecipent($senderid){
        $player = Player::getPlayerLogged();
        $messages = app(Message::class)->getAllMessageSenderForRecipient($senderid, $player->user);
        return $messages;
    }

    public function getAllMessageNotRead()
    {
        $player = Player::getPlayerLogged();
        $messages = app(Message::class)->getAllMessageNotRead($player->user);
        return  $messages;

    }

    public function newMessage(Request $request)
    {
        try {
            $recipientId = $request->input("recipientId") ;
            $this->messageService->newMessage($request, $recipientId);
        } catch (Exception $e) {
            return response(["msg" => "error " . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response(['message' => 'message send success!', 'success' => true], Response::HTTP_CREATED);
    }

    public function readMessage(Request $request)
    {
        $player = Player::getPlayerLogged();
        $msg = app(Message::class);
        $msg->readMessagesForUser($request->input("id"),$player->user);
        return response(['message' => 'message read!', 'success' => true], Response::HTTP_OK);
    }

    public function getSenders(){
        $player = Player::getPlayerLogged();
        $messages = app(Message::class)->getSenders($player->user);
        return $messages;
    }

    public function getConversation($senderId){
        $player = Player::getPlayerLogged();
        $messages = app(Message::class)->getConversation($player->user, $senderId);
        return $messages;
    }

    public function getLastMessageNotReadBySender($senderid){
        $message = app(Message::class);
        $player = Player::getPlayerLogged();
        $msg = $message->getLastMessageNotReadBySender($player->user,$senderid);
        return $msg;
    }

    public function searchUser($string){
        return  $this->messageService->searchUser($string);
    }
    
}
