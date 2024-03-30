<?php

namespace App\Services;

use App\Models\Player;
use App\Models\LoginResult;
use Illuminate\Support\Facades\Auth;

class UserService
{

    public function createToken($request): LoginResult
    {
        $result = new LoginResult();
        $result->success = false;
        $result->verified_email = true;
        $result->message  = 'Username or password incorrect. Please try again.';

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user   = Auth::user();

            if(!$user->hasVerifiedEmail())
            {
                $result->message  = 'E-mail is not verified';
                $result->verified_email = false;
                return $result;
            }
            $planets = Player::getMyPlanets();

            $result->token   =  $user->createToken('AppCoreTritium')->plainTextToken;
            $result->name    =  $user->name;
            $result->planet  =  $planets[0]->id;
            $result->message = '';
            $result->success = true;

            return $result;
        }

        return $result;
    }
}
