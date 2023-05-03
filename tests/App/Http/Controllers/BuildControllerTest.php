<?php

namespace Tests\App\Http\Controllers;

use App\Http\Controllers\BuildController;
use Tests\TestCase;
use App\Models\User;
use App\Services\UserService;

# php artisan test --filter=BuildControllerTest
class BuildControllerTest extends TestCase
{
    # php artisan test --filter=BuildControllerTest::test_build
    public function test_build()
    {
        //Nicplayer1
        //$token = '10|dtwTRCLnT5Iec9yIUkzmQSh5voYpfy6iVo57C5mV';
        //NIcplayer2
        $token = '11|O7kUeAVFnGw2GIw35eQ205hMrhwnWjcPjMcY3962';
        $data = [
            'planet' => 2,
            'build' => 1,
            'slot' => 1
        ];
        $response = $this->withHeaders(['Authorization'=>'Bearer '.$token,
                                        'Accept' => 'application/json'])
                                        ->post('/api/build/plant', $data);
        $response->dump();   
        $response->assertStatus(200);
    }

    # php artisan test --filter=BuildControllerTest::test_auth_user
    public function test_auth_user(){ 

         $loginData = ['email' => 'nicplayer2@gmail.com', 'password' => '123456'];

         $response = $this->json('POST', 'api/user/login', $loginData, ['Accept' => 'application/json']);
           
        $response->dump();

        // $user = User::select()->first();
        // $serv =  new UserService(); 
        // $login->password = '123456' ;
        // $login->email = 'nicplayer2@com.br';

        // $token = $serv->createToken($login);
        // dump($user->name);
        $response->assertStatus(200);

    }

    # php artisan test --filter=BuildControllerTest::test_create_player
    public function test_create_player(){ 

        $createPlayer = [  'email' => 'nicplayer2@gmail.com', 
                        'name' => 'nicplayer2',
                        'country'=> 2,
                        'address'];

        $response = $this->json('POST', 'api/player/register', $createPlayer, ['Accept' => 'application/json']);
        $response->dump();
        $response->assertStatus(200);

   }
}