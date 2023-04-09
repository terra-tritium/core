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
    public function show(String $address)
    {
        return Player::where("address", $address)->first();
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

        $user = new User();
        $user->email    = $request->input("email");
        $user->name     = $request->input("name");
        $user->password = bcrypt($request->input("password"));
        $user->save();
        
        $player = new Player();
        $player->address = $request->input("address");
        $player->country = $request->input("country");
        $player->name = $request->input("name");
        $player->user_id = $user->id;
        $this->playerService->register($player);
        
        return response(['messagem' => 'Player created success!','success'=>true],200);

    }
}
