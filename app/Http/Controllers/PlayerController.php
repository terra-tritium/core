<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\User;
use App\Services\PlayerService;
use App\Services\UserService;
use Illuminate\Http\Request;

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

    public function register(Request $request) {

        $userExist   =  User::where('email',$request->input("email"))->first();
        $playedExist =  Player::where('name',$request->input("name"))->first();

        if($userExist){
            return response(['message' => 'There is already a registered user with this E-mail!','success'=>false],200);
        }

        if($playedExist){
            return response(['message' => 'There is already a registered user with this Player Name!','success'=>false],200);
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
        
        return response(['message' => 'Player created success!','success'=>true],200);

    }

}
