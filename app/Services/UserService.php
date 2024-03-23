<?php

namespace App\Services;

use App\Models\Player;
use App\Models\ReturnResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;


class UserService
{

    public function createToken($request): ReturnResult
    {
        $result = new ReturnResult();
        $result->success = false;
        $result->message  = 'Invalid Credentials';
        $result->response = Response::HTTP_OK;
        $result->data = ['token'=>'','name'=>'','planet'=>''];

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user   = Auth::user();

            if(!$user->hasVerifiedEmail())
            {
                $result->success = false;
                $result->message  = 'E-mail is not verified';

                return $result;
            }
            $planets = Player::getMyPlanets();

            $success['token']   =  $user->createToken('AppCoreTritium')->plainTextToken;
            $success['name']    =  $user->name;
            $success['planet']  =  $planets[0]->id;

            $result->message  = '';
            $result->success = true;
            $result->data  =  $success;
            return $result;
        }
        return $result;
    }
}
