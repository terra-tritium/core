<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\RobotFactoryService;

class FactoryController extends Controller
{
    protected $robotFactoryService;

    public function __construct(RobotFactoryService $robotFactoryService)
    {
        $this->robotFactoryService = $robotFactoryService;
    }

    public function energy($planet, $qtd)
    {
        $resp = $this->robotFactoryService->setEnergy($planet, $qtd);

        return response()->json([
            'status' => 'success',
            'data' => [
                'qtd' => $resp
            ]
        ]);
    }
}
