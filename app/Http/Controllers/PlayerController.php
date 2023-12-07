<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\User;
use App\Services\PlayerService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Schema(
 *         schema="RegisterPlayerRequest",
 *         @OA\Property(
 *             property="email",
 *             type="string",
 *             example="example@example.com"
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             example="John Doe"
 *         ),
 *         @OA\Property(
 *             property="password",
 *             type="string",
 *             example="password"
 *         ),
 *         @OA\Property(
 *             property="address",
 *             type="string",
 *             example="123 Street"
 *         ),
 *         @OA\Property(
 *             property="country",
 *             type="string",
 *             example="USA"
 *         )
 *     )
 *
 *
 * )
 */

class PlayerController extends Controller
{

    protected $playerService;
    protected $userService;

    public function __construct(PlayerService $playerService, UserService $userService)
    {
        $this->playerService = $playerService;
        $this->userService  = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Models\Player  $player
     * @return \Illuminate\Http\Response
     *
     * @OA\Get (
     *     path="/api/player/show",
     *     tags={"Players"},
     *     summary="List Players",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(response="200", description="Resposta bem-sucedida")
     * )
     *  @OA\SecurityScheme(
     *     type="http",
     *     scheme="bearer",
     *     securityScheme="bearerAuth"
     * )
     *
     */
    public function show()
    {
        return Player::getPlayerLogged();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Player  $player
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Player $player)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Player  $player
     * @return \Illuminate\Http\Response
     */
    public function destroy(Player $player)
    {
        //
    }

    /**
     *
     * @OA\Post(
     *     path="/player/new",
     *     operationId="registerPlayer",
     *     tags={"Players"},
     *     summary="Register a new player",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RegisterPlayerRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Player created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Player created success!"
     *             ),
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User or player already exists",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="There is already a registered user with this E-mail!"
     *             ),
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             )
     *         )
     *     )
     * )
     *
     * @param Request $request
     * @return Response
     */
    public function register(Request $request) {

        $validator =Validator::make($request->all(), [
            'name' => 'required',
            'country' => 'required',
            'email' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->messages()->first(),'success'=>false], 
            Response::HTTP_OK);
        }

        $userExist   =  User::where('email',$request->input("email"))->first();
        $playedExist =  Player::where('name',$request->input("name"))->first();

        if($userExist){
            return response(['message' => 'There is already a registered user with this E-mail!','success'=>false],
                Response::HTTP_OK);
        }

        if($playedExist){
            return response(['message' => 'There is already a registered user with this Player Name!','success'=>false],
                Response::HTTP_OK);
        }

        $user = new User();
        $user->email    = $request->input("email");
        $user->name     = $request->input("name");
        $user->password = bcrypt($request->input("password"));
        $user->save();

        $player = new Player();
        $player->address = $request->input("address");
        $player->country = $request->input("country");
        $player->name = $request->input("name");
        $player->user = $user->id;
        $this->playerService->register($player);
//subir
        return response(['message' => 'Player created success!','success'=>true],Response::HTTP_OK);

    }

    /**
     *
     *  @OA\Post(
     *     path="/player/list-name/{id}",
     *     operationId="getPlayerName",
     *     tags={"Players"},
     *     summary="Get user name by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="John Doe"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     *
     * @param $userId
     * @return mixed
     */
    public function getNameUser($userId) {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'],
                Response::HTTP_NOT_FOUND);
        }

        return response()->json(['name' => $user->name]);
    }

    /**
     *
     * @OA\Get(
     *     path="/player/details/{id}",
     *     operationId="getPlayerDetails",
     *     tags={"Players"},
     *     summary="Get player details by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Player ID",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Player not found"
     *     )
     * )
     *
     * @param $id
     * @return array
     */
    public function getDetails($id) {
        try {
            $playerDetails = $this->playerService->getDetails($id);
            return response()->json($playerDetails);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Player not found'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     *
     * @OA\Post(
     *     path="/player/change-name",
     *     operationId="changeName",
     *     tags={"Players"},
     *     summary="Change name player",
     *     @OA\Parameter(
     *         name="name",
     *         required=true,
     *         description="Player's name",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Player not found"
     *     )
     * )
     *
     * @param $id
     * @return array
     */
    public function changeName(Request $request)
    {
        try {
            $player = Player::getPlayerLogged();
            if (!$player) {
                return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
            }
            $name = $request->name;
            $result = $this->playerService->changeName($player->id, $name);

            return response()->json($result, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * @todo retirar ao subir para produção
     */
    public function showAll(){
        $player = new Player();
        $players = $player::all();
        return response()->json(["Count" => count($players), "Players" => $players], Response::HTTP_OK);
    }

}
