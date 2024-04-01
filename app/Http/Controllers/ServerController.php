<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class ServerController extends Controller
{
    public function list() {
        try {
            return response()->json([
                [
                    'id' => 1,
                    'name' => 'Rigel',
                    "slug" => "rigel",
                    'type' => 'Beta',
                    "default" => true
                ],
                [
                    'id' => 2,
                    'name' => 'Sirius',
                    "slug" => "sirius",
                    'type' => 'Test',
                    "default" => false
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error on list servers.'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
