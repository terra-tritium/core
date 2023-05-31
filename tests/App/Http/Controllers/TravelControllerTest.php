<?php

namespace Tests\App\Http\Controllers;

use App\Http\Controllers\BuildController;
use Tests\TestCase;
use App\Models\User;
use App\Services\UserService;

# php artisan test --filter=TravelControllerTest
class TravelControllerTest extends TestCase
{
    # php artisan test --filter=TravelControllerTest::test_travel
    public function test_travel()
    {
        $loginData = ['email' => 'nicplayer@gmail.com', 'password' => '123456'];

        $response = $this->json('POST', 'api/user/login', $loginData, ['Accept' => 'application/json']);
        
        $content = json_decode($response->getContent(), true);
        $token = $content['token'];

        $data = [
                    'action'    => 1,
                    'to'        => 'A000:2',
                    'from'      => 'A000:1',
                    'troop' =>[
                        ['unit' => 1, 'quantity' => 20],
                        ['unit' => 2, 'quantity' => 20],
                    ] 
                ];
        
        $response = $this->withHeaders(['Authorization'=>'Bearer '.$token,
                                        'Accept' => 'application/json'])
                                        ->post('/api/travel/start', $data);
        $response->dump();   
        $response->assertStatus(200);
    }


}