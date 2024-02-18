<?php

namespace Tests\App\Http\Controllers;

use App\Http\Controllers\BuildController;
use Tests\TestCase;
use App\Models\User;
use App\Services\UserService;
use App\Services\PlanetService;

# php artisan test --filter=TroopControllerTest
class TroopControllerTest extends TestCase
{
    # php artisan test --filter=TroopControllerTest::test_build
    public function test_build()
    {
        $token = '6|aNHo6V3dmVeMkovYIZpVo45q0JRAiOalS0TOp4Hj8bce9223';

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

    # php artisan test --filter=TroopControllerTest::test_funcionalidade
    public function test_funcionalidade()
    {
        $prodSrv = new PlanetService();
        $tempo =  $prodSrv->calculeDistance(3,11);
        var_dump(now()->addSeconds(59));
    }

}