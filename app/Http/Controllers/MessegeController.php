<?php

namespace App\Http\Controllers;

use App\Models\Planet;
use App\Models\Player;
use Illuminate\Http\Request;
use App\Models\Message;
use DateTime;
use Exception;

class MessegeController extends Controller
{

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

    public function list()
    {
        $player = Player::getPlayerLogged();

        return Planet::where('player', $player->id)->get();
    }

    public function ping()
    {
        $msg = new Message();
        $dados = $msg->getMsg();
        return $dados;
    }


    public function getAll()
    {
        $msg = new Message();
        return $msg->getAll();
    }

    public function getAllByUserSender($id)
    {
        $msg = new Message();
        return $msg->getAllByUserSender($id);
    }

    public function getAllByUserRecipient($id)
    {
        $msg = new Message();
        return $msg->getAllByUserRecipient($id);
    }

    public function getAllMessegeNotRead($id){
        return (new Message())->getAllMessegeNotRead($id);
    }
    public function newMessege(Request $request)
    {
        try {
            $msg = new Message();
            $msg->senderId = $request->input("senderId");
            $msg->recipientId = $request->input("recipientId");
            $msg->content = $request->input("content");
            $msg->status = true;
            $msg->read = false;
            $msg->save();
        } catch (Exception $e) {
            return response(["msg" => "error " . $e->getMessage()], 500);
        }
        return response(['message' => 'message send success!', 'success' => true], 201);
    }
    public function readMessege(Request $request){
        $msg = (new Message())->find($request->input("id"));
        $msg->read = true;
        $msg->readAt = date('Y-m-d H:i:s');
        $msg->save();
        return response(['message' => 'message read!', 'success' => true], 200);
    }
}
