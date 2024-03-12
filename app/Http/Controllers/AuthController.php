<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\VerificationNotificationJob;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\Quadrant;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Log;


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
        $result = $this->userService->createToken($request);

        return response([
                            'message' => $result->message,
                            'success'=>$result->success,
                            'token' => $result->data['token'],
                            'name' => $result->data['name'],
                            'planet' => $result->data['planet']
                        ],
                        $result->response);
    }

    public function logout(Request $request)
    {
        auth('sanctum')->user()->tokens()->delete();

        return response(['message' => 'Tokens Revoked','success'=>true],Response::HTTP_OK);
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

        return response(['success' => $success  ,'message' => trans($status) ]);
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
            return response(['message' => 'No user found'], Response::HTTP_NOT_FOUND);
        }

        $token = $user->createToken('TokenName')->plainTextToken;

        return response(['message' => 'Token generated successfully', 'token' => $token], Response::HTTP_OK);
    }

    public function sendLinkVerifyEmailRestister($email)
    {
        try{

            $user = User::where('email',$email)->first();
            VerificationNotificationJob::dispatch($user);

            return response(['message' => 'Another email was sent with the link to verify the email.','success'=>true],Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function verifyEmail($id,$hash,Request $request)
    {
        try{
            if (!$request->hasValidSignature()) {
                return response(['message' => 'Invalid/Expired url provided.','success'=>false],401);
            }

            $user = User::findOrFail($id);

            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            return response(['message' => 'E-mail verify with success.','success'=>true],Response::HTTP_OK);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
