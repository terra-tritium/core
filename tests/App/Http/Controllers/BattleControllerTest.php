<?php

namespace Tests\App\Http\Controllers;

use App\Http\Controllers\BuildController;
use Tests\TestCase;
use App\Models\User;
use App\Services\UserService;

# php artisan test --filter=BattleControllerTest
class BattleControllerTest extends TestCase
{
    # php artisan test --filter=BattleControllerTest::test_build
    public function test_build()
    {
        $loginData = ['email' => 'nicplayer2@gmail.com', 'password' => '123456'];

        $response = $this->json('POST', 'api/user/login', $loginData, ['Accept' => 'application/json']);
        
        $content = json_decode($response->getContent(), true);
        $token = $content['token'];

        $data = [
           
        ];

        $response = $this->withHeaders(['Authorization'=>'Bearer '.$token,
                                        'Accept' => 'application/json'])
                                        ->post('/api/troop/production/1', $data);
        $response->dump();   
        $response->assertStatus(200);
    }

    # php artisan test --filter=BattleControllerTest::test_battle_start
    public function test_battle_start()
    {
        $loginData = ['email' => 'nicplayer@gmail.com', 'password' => '123456'];

        $response = $this->json('POST', 'api/user/login', $loginData, ['Accept' => 'application/json']);
        
        $content = json_decode($response->getContent(), true);
        $token = $content['token'];
        $defense = 2 ;
        $planet_defense  = 2 ;

        $response = $this->withHeaders(['Authorization'=>'Bearer '.$token,
                                        'Accept' => 'application/json'])
                                        ->get("/api/battle/start/{$defense}/{$planet_defense}");
        $response->dump();   
        $response->assertStatus(200);
    }

}