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
use App\Notifications\SendMail;
use App\Notifications\Message;
use App\Models\Quadrant;

class AuthController extends Controller
{
    protected $userService;

    // @todo Geracao de posicao dos quadrantes (remover antes de ir para producao)
    public function gerar () {
        function distancia($coord1, $coord2) {
        $dx = $coord1['x'] - $coord2['x'];
        $dy = $coord1['y'] - $coord2['y'];
        return sqrt($dx * $dx + $dy * $dy);
        }

        $coords = array();

        while(count($coords) < 100) {
        $x = rand(-200, 200);
        $y = rand(-200, 200);
        $coord = array('x' => $x, 'y' => $y);

        if(!in_array($coord, $coords)) {
            array_push($coords, $coord);
        }
        }

        usort($coords, function($a, $b) use($coords) {
        $dist1 = 0;
        $dist2 = 0;
        foreach($coords as $coord) {
            $dist1 += distancia($coord, $a);
            $dist2 += distancia($coord, $b);
        }
        return $dist1 - $dist2;
        });

        $cont = 1500;

        foreach($coords as $coord) {
            $cont++;
            $quadrant = Quadrant::find($cont);
            echo "DB::table('qnames')->insert([\"x\" => \"".$coord['x']."\", \"y\" => \"".$coord['y']."\", \"quadrant\" => \"$quadrant->quadrant\", \"name\" => \"$quadrant->name\"]);\n";
        }
    }

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function createToken(Request $request)
    {
        $success = $this->userService->createToken($request);

        if($success)
        {
            return response(['message' => '','success'=>true, 'token' => $success['token'],'name' => $success['name'],'planet' => $success['planet']],200);
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
            'password' => 'required|confirmed',
        ],[
            "password" => 'The password confirmation does not match.',
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

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @todo remover antes de enviar para produção
     *
     * @OA\Get (
     *      path="/api/generate-token",
     *      summary="Generate Token",
     *      tags={"Auth"},
     *      description="Generate token to access other endpoints",
     * @OA\Response(response="200", description="Sucesso")
     * )
     *
     */
    public function generateToken()
    {
        $user = User::first();
        if (!$user) {
            return response(['message' => 'No user found'], 404);
        }

        $token = $user->createToken('TokenName')->plainTextToken;

        return response(['message' => 'Token generated successfully', 'token' => $token], 200);
    }

}
