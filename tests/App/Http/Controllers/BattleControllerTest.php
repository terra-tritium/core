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
        $token = '2|zC4C0XBslpE141CftkJRNPUV7aSFmxenuvveaYa3';

        $data = [
            'planetId' => 5,
            'build' => 1,
            'slot' => 1
        ];
        $response = $this->withHeaders(['Authorization'=>'Bearer '.$token,
                                        'Accept' => 'application/json'])
                                        ->post('/api/build/plant', $data);
        $response->dump();   
        $response->assertStatus(200);
    }

}