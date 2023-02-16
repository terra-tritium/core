<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;

class UserService
{

    public function createToken($request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('AppCoreTritium')->plainTextToken; 
            $success['name'] =  $user->name;
            
            return $success;
            
        } 
        else{ 
           return false;
        } 
    }
}