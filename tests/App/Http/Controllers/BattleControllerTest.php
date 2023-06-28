<?php

namespace Tests\App\Http\Controllers;

use App\Http\Controllers\BuildController;
use Tests\TestCase;
use App\Models\User;
use App\Services\BattleService;
use App\Services\TravelService;
use App\Services\UserService;


# php artisan test --filter=BattleControllerTest
class BattleControllerTest extends TestCase
{
    # php artisan test --filter=BattleControllerTest::test_build
    public function test_build()
    {
        $loginData = ['email' => 'nicplayer2@gmail.com', 'password' => '123'];

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

        $battleService = new BattleService();
        $travelService = new TravelService($battleService);   
        $response = $travelService->starBattleTravel(1);
        
        dd('Test end');
    }
    
    # php artisan test --filter=BattleControllerTest::test_create_state
    public function test_create_state()
    {
        $battleService = new BattleService();   
        $battle = \App\Models\Battle::select()->first();
        $response = $battleService->calculateStage($battle->id);
        dd('Test end');
        
    }

}