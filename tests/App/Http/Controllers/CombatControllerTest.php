<?php

namespace Tests\App\Http\Controllers;

use App\Http\Controllers\BuildController;
use Tests\TestCase;
use App\Models\User;
use App\Services\CombatService;
use App\Services\TravelService;
use App\Services\UserService;


# php artisan test --filter=CombatControllerTest
class CombatControllerTest extends TestCase
{
    # php artisan test --filter=CombatControllerTest::test_build
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

    # php artisan test --filter=CombatControllerTest::test_combat_start
    public function test_combat_start()
    {

        $combatService = new CombatService();
        $travelService = new TravelService($combatService);   
        $response = $travelService->starCombatTravel(1);
        
        dd('Test end');
    }
    
    # php artisan test --filter=CombatControllerTest::test_create_state
    public function test_create_state()
    {
        $combatService = new CombatService();   
        $combat = \App\Models\Combat::select()->first();
        $response = $combatService->calculateStage($combat->id);
        dd('Test end');
        
    }

}