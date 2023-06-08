<?php

namespace Tests\App\Http\Controllers;

use App\Http\Controllers\BuildController;
use Tests\TestCase;
use App\Models\User;
use App\Services\UserService;

# php artisan test --filter=BuildControllerTest
class BuildControllerTest extends TestCase
{
    # php artisan test --filter=BuildControllerTest::test_create_player
    public function test_create_player(){ 

        $createPlayer = [  'email' => 'nicplayer2@gmail.com', 
                            'name' => 'nicplayer2',
                            'country'=> 2,
                            'password' => '123456',
                            'address'];

        $response = $this->json('POST', 'api/player/register', $createPlayer, ['Accept' => 'application/json']);
        $response->dump();
        $response->assertStatus(200);

   }

    # php artisan test --filter=BuildControllerTest::test_build
    public function test_build()
    {
        $loginData = ['email' => 'nicplayer2@gmail.com', 'password' => '123456'];

        $response = $this->json('POST', 'api/user/login', $loginData, ['Accept' => 'application/json']);
        
        $content = json_decode($response->getContent(), true);
        $token = $content['token'];
        
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
    
    # php artisan test --filter=BuildControllerTest::test_build_troop_production
    public function test_build_troop_production()
    {
        $loginData = ['email' => 'nicplayer2@gmail.com', 'password' => '123456'];

        $response = $this->json('POST', 'api/user/login', $loginData, ['Accept' => 'application/json']);
        
        $content = json_decode($response->getContent(), true);
        $token = $content['token'];
        $planet = 2 ;

        $data = ['id'=> 2, 'quantity' => 30 ];
        
        $response = $this->withHeaders(['Authorization'=>'Bearer '.$token,
                                        'Accept' => 'application/json'])
                                        ->post('/api/troop/production/'.$planet, $data);
        $response->dump();   
        $response->assertStatus(200);
    }

    # php artisan test --filter=BuildControllerTest::test_build_producing
    public function test_build_producing()
    {
        $loginData = ['email' => 'nicplayer@gmail.com', 'password' => '123456'];

        $response = $this->json('POST', 'api/user/login', $loginData, ['Accept' => 'application/json']);
        
        $content = json_decode($response->getContent(), true);
        $token = $content['token'];
        $planet = 2 ;
        $response = $this->withHeaders(['Authorization'=>'Bearer '.$token,
                                        'Accept' => 'application/json'])
                                        ->get('/api/troop/production/'.$planet);
        $response->dump();   
        $response->assertStatus(200);
    }

}