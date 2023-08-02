<?php

namespace App\Services;

use App\Models\User;
use App\Models\Planet;
use App\Models\Player;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserService
{

    public function createToken($request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user   = Auth::user();
            $planets = Player::getMyPlanets();

            $success['token']   =  "teste";
            $success['name']    =  $user->name;
            $success['planet']  =  $planets[0]->id;

            return $success;
            $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

            $token = $user->createToken('AppCoreTritium')->plainTextToken;


            $planets = Player::getMyPlanets();

            $success['token']   =  $token;
            $success['name']    =  $user->name;
            $success['planet']  =  $planets[0]->id;

            return $success;

        }
        else{
           return false;
        }
    }
}
