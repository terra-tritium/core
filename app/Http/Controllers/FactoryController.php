<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\RobotFactoryService;
use Illuminate\Support\Facades\Log;

class FactoryController extends Controller
{
    protected $robotFactoryService;

    public function __construct(RobotFactoryService $robotFactoryService)
    {
        $this->robotFactoryService = $robotFactoryService;
    }

    public function createHumanoid($planet, $qtd)
    {
        try {
            $resp = $this->robotFactoryService->createHumanoid($planet, $qtd);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'qtd' => $resp
                ]
            ]);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(['message' => 'Error on create humanoid'], 500);
        }
    }
}
