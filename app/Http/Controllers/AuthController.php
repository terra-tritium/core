<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function createToken(Request $request)
    {
        $success = $this->userService->createToken($request);

        if($success)
        {
            return response([$success]);
        }else{
            return response(['message' => 'Invalid Credentials','success'=>false],401);
        }

    }

}
