<?php

namespace App\Http\Controllers;

use App\Models\Aliance;
use App\Models\AlianceMember;
use App\Models\AlianceRequest;
use App\Models\Building;
use App\Models\Logo;
use App\Models\Planet;
use App\Models\Player;
use App\Models\RankMember;
use App\Services\AlianceService;
use App\Services\ChatAlianceService;
use App\Services\FleetService;
use App\Services\MessageService;
use App\Services\RankingService;
use App\Services\TroopService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;
use DateTime;


/**
 *   @OA\Schema(
 *     schema="Aliances",
 *     required={"name"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="logo", type="file")
 * )
 *
 * @OA\Schema(
 *     schema="AliancesUpdateRequest",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", example="New Name"),
 *     @OA\Property(property="description", type="string", example="New Description"),
 * )
 */
class AliancesController extends Controller
{
    protected $alianceService;
    public function __construct(AlianceService $alianceService)
    {
        $this->alianceService = $alianceService;
    }
    /**
     * * @OA\Get(
     *     path="/aliances/list",
     *     tags={"Aliances"},
     *     summary="List all alliances",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Aliances")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error finding alliances",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error finding alliances")
     *         )
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {

            // $aliances = Aliance::all();
            $aliance = new Aliance();
            $dadosAlianca = $aliance->getAliances();
            $dadosAlianceBuild = $this->alianceService->getDadosBuildsAliance($dadosAlianca);
            return response()->json($dadosAlianceBuild, Response::HTTP_OK);
        } catch (Throwable $exception) {
            Log::error($exception);

            return response()->json(
                ['message' => 'Error finding alliances'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function readMessage($id) {
        
        try {
            $player = Player::find($id);
            $player->aliance_new_message = 0;
            $player->save();
            return response()->json("OK", Response::HTTP_OK);
        } catch (Throwable $exception) {
            Log::error($exception);

            return response()->json(
                ['message' => 'Erro ao setar mensagem da alianca como lida'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function newMessage($id) {
        try {
            $player = Player::find($id);
            Player::where('aliance', $player->aliance)->update(['aliance_new_message' => 1]);
            return response()->json("OK", Response::HTTP_OK);
        } catch (Throwable $exception) {
            Log::error($exception);

            return response()->json(
                ['message' => 'Erro ao setar mensagem da alianca como lida'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function donate($code, $planet, $qtd) {
        try {
            $loggedPlayer = Player::getPlayerLogged();
            $result = $this->alianceService->donate($code, $planet, $qtd, $loggedPlayer->aliance);
            return response()->json($result, Response::HTTP_OK);
        } catch (Throwable $exception) {
            Log::error($exception);

            return response()->json(
                ['message' => 'Erro ao setar mensagem da alianca como lida'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     *
     *@OA\Post(
     *     path="/aliances/create",
     *     tags={"Aliances"},
     *     summary="Create an alliance",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="logo", type="file")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Alliance created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Aliances")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error: failed to create alliance",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error: failed to create alliance")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        try {
            return $this->alianceService->founderAliance($request);
        } catch (Throwable $exception) {
            Log::error($exception);
            return response()->json(
                ['message' => 'Error : failed to create alliances'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     *
     * * @OA\Put(
     *     path="/aliances/edit/{id}",
     *     tags={"Aliances"},
     *     summary="Update an alliance",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the alliance to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AliancesUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Alliance updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Aliances")
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error: failed to update alliance",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error: failed to update alliance")
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $aliances = Aliance::findOrFail($id);

        try {
            $aliances->fill($request->all([
                'name',
                'description',
                'logo'
            ]));

            if ($request->has('name')) {
                $aliances->name = $request->input('name');
            }

            if ($request->has('description')) {
                $aliances->description = $request->input('description');
            }

            if ($request->has('type')) {
                $aliances->type = $request->input('type');
            }

            if ($request->hasFile('logo')) {
                $imagePath = $request->file('logo')->store('public/uploads');
                $aliances->logo = $imagePath;
            }

            $aliances->save();

            return response()->json($aliances, Response::HTTP_OK);
        } catch (Throwable $exception) {
            Log::error($exception);
            return response()->json(
                ['message' => 'Error : failed to update alliances'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     *
     * @OA\Delete(
     *     path="/aliances/delete/{id}",
     *     tags={"Aliances"},
     *     summary="Delete an alliance",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the alliance to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Alliance deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Alliance deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error deleting alliance",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error deleting alliance")
     *         )
     *     )
     * )
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {

        try {
            return $this->alianceService->deleteAliance($id);
        } catch (Throwable $exception) {
            Log::error($exception);
            return response()->json(
                ['message' => 'Error deleting Alliances'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function updateLogo(Request $request, int $id)
    {
        $aliances = Aliance::findOrFail($id);

        try {
            if ($request->file('logo')) {
                $imagePath = $request->file('logo')->store('images');
                $aliances->logo = $imagePath;
            }

            $aliances->save();

            return response()->json($aliances, Response::HTTP_OK);
        } catch (Throwable $exception) {
            Log::error($exception);
            return response()->json(
                ['message' => 'Error: failed to update logo'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


    /**
     *
     * @OA\Post(
     *     path="/aliances/join",
     *     operationId="joinAliance",
     *     tags={"Aliances"},
     *     summary="Join an alliance",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="alianca_id",
     *                     type="integer",
     *                     description="ID of the alliance",
     *                 ),
     *                 example={"alianca_id": 123}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Player joined the alliance successfully",
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Bad request",
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function joinAliance(Request $request)
    {
        return $this->alianceService->joinAlliance($request->input('alianca_id'),false);


        /* if ($player->leave_date) {
            $leaveDate = Carbon::parse($player->leave_date);

            if (Carbon::now()->diffInDays($leaveDate) < 5) {
                return response()->json(
                    ['message' => 'The player must wait 5 days before joining a new alliance.'],
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        if ($aliance->type == 0) {
            $player->aliance = $aliancaId;
            $player->save();
            return response()->json(['message' => 'Player joined the alliance successfully'], Response::HTTP_OK);
        } elseif ($aliance->type == 1) {

            $existingRequest = DB::table('aliances_requests')
                ->where('player_id', $player->id)
                ->where('aliance_id', $aliancaId)
                ->first();

            if ($existingRequest) {
                return response()->json(['message' => 'Request already sent. Wait for founder approval.']);
            }

            $this->sendAliancesRequest($player->id, $aliance->founder);

            return response()->json(['message' => 'Request sent to alliance founder'], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Invalid alliance type'], Response::HTTP_BAD_REQUEST);
        }*/
    }

    /**
     *
     * @OA\Post(
     *     path="/aliances/request",
     *     operationId="handlePlayerRequest",
     *     tags={"Aliances"},
     *     summary="Handle player request for alliance",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="player_id",
     *                     type="integer",
     *                     description="ID of the player",
     *                 ),
     *                 @OA\Property(
     *                     property="aliance_id",
     *                     type="integer",
     *                     description="ID of the alliance",
     *                 ),
     *                 @OA\Property(
     *                     property="accept_request",
     *                     type="boolean",
     *                     description="Whether to accept the player request or not",
     *                 ),
     *                 example={"player_id": 123, "aliance_id": 456, "accept_request": true}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Player accepted into the alliance",
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Failed to accept player into alliance",
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handlePlayerRequest(Request $request, AlianceService $alianceService)
    {
        /** @todo aqui */
        $playerId = $request->input('player_id');
        $alianceId = $request->input('aliance_id');
        $acceptRequest = $request->input('accept_request'); // true or false

        if ($acceptRequest) {
            $result = $alianceService->acceptPlayerRequest($playerId, $alianceId);

            if ($result) {
                DB::table('aliances_requests')
                    ->where('player_id', $playerId)
                    ->where('aliance_id', $alianceId)
                    ->update([
                        'status' => 'accepted',
                        'message' => 'Request accepted by founder.',
                    ]);

                Player::where('id', $playerId)->update(['aliance' => $alianceId]);

                return response()->json(['message' => 'Player accepted into the alliance.']);
            } else {
                DB::table('aliances_requests')
                    ->where('player_id', $playerId)
                    ->where('aliance_id', $alianceId)
                    ->update([
                        'status' => 'failed',
                        'message' => 'Failed to accept player into alliance.',
                    ]);

                return response()->json(['message' => 'Failed to accept player into alliance.']);
            }
        } else {
            DB::table('aliances_requests')
                ->where('player_id', $playerId)
                ->where('aliance_id', $alianceId)
                ->update([
                    'status' => 'rejected',
                    'message' => 'Request declined by founder.',
                ]);

            return response()->json(['message' => 'Player declined in alliance.']);
        }
    }

    /**
     *
     * @OA\Post(
     *     path="/aliances/leave",
     *     operationId="leaveAliance",
     *     tags={"Aliances"},
     *     summary="Leave an alliance",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="player_id",
     *                     type="integer",
     *                     description="ID of the player",
     *                 ),
     *                 example={"player_id": 123}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Player has successfully exited the alliance",
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Player not found",
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="The player is not in an alliance",
     *     )
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function leaveAliance(Request $request)
    {
        $playerId = $request->input('player_id');
        $player = Player::find($playerId);

        if (!$player) {
            return response()->json(['message' => 'Player not found.'], 404);
        }

        if (!$player->aliance) {
            return response()->json(['message' => 'The player is not in an alliance.'], 400);
        }

        $player->aliance = null;
        $player->leave_date = Carbon::now();
        $player->save();

        return response()->json(['message' => 'Player has successfully exited the alliance.']);
    }

    /**
     *
     *  @OA\Post(
     *     path="/aliances/kick-player",
     *     operationId="kickPlayer",
     *     tags={"Aliances"},
     *     summary="Kick a player from the alliance",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="player_id",
     *                     type="integer",
     *                     description="ID of the player to be kicked",
     *                 ),
     *                 @OA\Property(
     *                     property="aliance_id",
     *                     type="integer",
     *                     description="ID of the alliance",
     *                 ),
     *                 example={"player_id": 123, "aliance_id": 456}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Player successfully kicked out of the alliance",
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Player not found",
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="The player does not belong to this alliance",
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="You are not allowed to kick players from this alliance",
     *     )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function kickPlayer(Request $request)
    {
        $playerId = $request->input('player_id');
        $aliancId = $request->input('aliance_id');

        $loggedPlayer = Player::getPlayerLogged();

        $founderId = $loggedPlayer->id;
        $aliance = Aliance::find($aliancId);

        if (!$aliance || $aliance->founder !== $founderId) {
            return response()->json(
                ['message' => 'You are not allowed to kick players from this alliance.'],
                Response::HTTP_FORBIDDEN
            );
        }

        $player = Player::find($playerId);

        if (!$player) {
            return response()->json(['message' => 'Player not found.'], Response::HTTP_NOT_FOUND);
        }

        if ($player->aliance !== $aliancId) {
            return response()->json(
                ['message' => 'The player does not belong to this alliance.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $player->aliance = null;
        $player->save();

        return response()->json(
            ['message' => 'Player successfully kicked out of the alliance.'],
            Response::HTTP_OK
        );
    }

    /**
     *  @OA\Get(
     *     path="/aliances/{alianceId}/players",
     *     summary="Listar jogadores de uma aliança",
     *     tags={"Aliances"},
     *     @OA\Parameter(
     *         name="alianceId",
     *         in="path",
     *         description="ID da aliança",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de jogadores da aliança",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Player")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aliança não encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Aliança não encontrada.")
     *         )
     *     ),
     * )
     *
     * @param $aliancId
     * @return \Illuminate\Http\JsonResponse
     */
    public function listPlayers($alianceId)
    {
        $aliance = Aliance::find($alianceId);
        if (!$aliance) {
            return response()->json(['message' => 'Alliance not found.'], Response::HTTP_NOT_FOUND);
        }
        $players = Player::where('aliance', $alianceId)->get();
        return response()->json($players);
    }

    public function addImage($imageFile)
    {
        $fileName = time() . '_' . $imageFile->getClientOriginalName();
        $filePath = $imageFile->storeAs('images', $fileName);
        $fileUrl = Storage::url($filePath);

        return $fileUrl;
    }

    public function sendAliancesRequest($playerId, $founderId)
    {
        return DB::table('aliances_requests')->insert([
            'player_id' => $playerId,
            'founder_id' => $founderId,
            'message' => 'Solicitação de entrada na aliança',
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function getPlanetUserLogged()
    {
        $player = Player::getPlayerLogged();
        return Planet::where('player', $player->id)->get();
    }

    public function find($id) {
        $player = Player::find($id);
        $aliance = Aliance::find($player->aliance);
        return response()->json($aliance, Response::HTTP_OK);
    }

    public function myAliance()
    {
        $player = Player::getPlayerLogged();
        $aliance = Aliance::find($player->aliance);
        if (!$aliance) {
            //nenhuma aliança vinculada
            $alianceMemberPending = AlianceMember::where([['player_id', '=', $player->id], ['status', '=', 'P']])->first();
            if ($alianceMemberPending) {
                return response()->json($alianceMemberPending, Response::HTTP_OK);
            } else {
                return response()->json(['message' => 'Does not have allaince.'], Response::HTTP_NO_CONTENT);
            }
        }
        return response()->json($aliance, Response::HTTP_OK);
    }

    public function alianceDetailsCreated()
    {
        $player = Player::getPlayerLogged();
        return $this->alianceService->getDetailsMyAliance($player->id);
    }
    public function listMembers($alianceId)
    {
        return response()->json($this->alianceService->getMembersAliance($alianceId), Response::HTTP_OK);
    }
    public function listMembersPending($alianceId)
    {
        return response()->json($this->alianceService->getMembersPending($alianceId), Response::HTTP_OK);
    }
    public function removeMember($memberId)
    {
        return $this->alianceService->removeMember($memberId);
    }

    public function allLogos()
    {
        $logos = Logo::where('available', true)->get();
        if (!$logos) {
            return response()->json(['message' => 'Logos not found.'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($logos, Response::HTTP_OK);
    }
    public function updateRequestMember($idMemberRequest, $action)
    {
        return $this->alianceService->updateRequestMember($idMemberRequest, $action);
    }
    public function getAvailableName($name)
    {
        return $this->alianceService->getAvailableName($name);
    }
    public function exit($alianceId)
    {
        return $this->alianceService->exit($alianceId);
    }
    public function cancelRequest()
    {
        return $this->alianceService->cancelRequest();
    }
    public function getScoresAliance()
    {
        $ranking = new RankingService();
        return $ranking->initScoresAliance();
    }
    public function getRanks()
    {
        $rankMember = RankMember::where('visible', true)->get();
        return response()->json($rankMember, Response::HTTP_OK);
    }
    public function getMembersRank($idAliance)
    {
        return $this->alianceService->getMembersRank($idAliance);
    }
    public function changeRankMember($idRank, $idMember, $idAliance)
    {
        return $this->alianceService->changeRankMember($idRank, $idMember, $idAliance);
    }
    public function deixarRank($idAliance, $idMember)
    {
        return $this->alianceService->deixarCargo($idAliance, $idMember);
    }
    public function getUnitsPlayer($playerId, $type)
    {
        if ($type == "fleet") {
            $fleetService = new FleetService();
            $fleets = $fleetService->getFleetPlayer($playerId);
            return response()->json($fleets, Response::HTTP_OK);
        }
        if ($type == "troop") {
            $troopService = new TroopService();
            $troops = $troopService->getTroopPlayer($playerId);
            return response()->json($troops, Response::HTTP_OK);
        }
    }
    public function newMessageGroup(Request $request)
    {
        $messageService = new MessageService();
        return $messageService->setMessageAlianceGroup($request);
    }
    public function getMessagesGroup($idAliance)
    {
        $messageService = new MessageService();
        return $messageService->getMessagesGroupAliance($idAliance);
    }
    public function delMessage($idMessage)
    {
        return (new MessageService())->deleteMessage($idMessage);
    }

    public function newMessageAliance(Request $request)
    {
        $chatAlianceService = new ChatAlianceService();
        return $chatAlianceService->newMessageAlianceChat($request);
    }

    public function getMessageWithAliance($destino)
    {
        $chatAlianceService = new ChatAlianceService();
        return $chatAlianceService->getMessageWithAliance($destino);
    }

    public function listAlianceForChat()
    {
        $aliance = new Aliance();
        $aliances = $aliance->listAlianceForChat();
        return response()->json($aliances, Response::HTTP_OK);
    }

    public function searchUser($string)
    {
        $result = [];
        $loggedPlayer = Player::getPlayerLogged();
        if (!$loggedPlayer) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }
        $aliancemember = new AlianceMember();
        $type = strstr($string, "@") ? "email" : "name";
        $result = $aliancemember->searchUser($loggedPlayer->id, $string, $type);
       
        return response()->json($result, Response::HTTP_OK);
    }
    public function invite(Request $request){
        $loggedPlayer = Player::getPlayerLogged();
        if (!$loggedPlayer) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }
        $alianceService = new AlianceService();
        return $alianceService->invite($request);
    }
    public function receivedInvitations(){
        try{
            $loggedPlayer = Player::getPlayerLogged();
            if (!$loggedPlayer) {
                return response()->json(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
            }
            if(!$loggedPlayer->aliance){
               $dados = $this->alianceService->getDataReceivedInvitationAliance($loggedPlayer);
               return response()->json($dados,Response::HTTP_OK);
            }
            return response()->json([], Response::HTTP_OK);
        }catch(Exception $e){
            return response()->json(['message' => 'Erro ao buscar convites '.$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }      
    }

    public function acceptInvite(Request $request){
        try{
            $loggedPlayer = Player::getPlayerLogged();
            if (!$loggedPlayer) {
                return response()->json(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
            }
            $alianceRequest = AlianceRequest::where('id','=',$request->input("idInvite"))->first();
            return $this->alianceService->joinAlliance($alianceRequest->alianceId,true);

        }catch(Exception $e){
            return response()->json(['message' => 'Erro ao aceitar convite '.$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
  
        }

    }
} 
