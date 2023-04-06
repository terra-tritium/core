<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\SendMail;
use App\Notifications\Message;

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
            return response(['message' => '','success'=>true, 'player' => $success],200);
        }else{
            return response(['message' => 'Invalid Credentials','success'=>false],200);
        }

    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return [
            'message' => 'Tokens Revoked'
        ];
    }

    public function sendLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
             $request->only('email')
        );

        return response(['success' => true  ,'message' => '','email' => $request->email ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        $success =  $status === Password::PASSWORD_RESET ? true : false;

        return response(['success' => $success  ,'message' => $status ]);
    }

}
