<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class ServerController extends Controller
{
    public function list() {
        try {
            return json_encode([
                'Rigel' => [
                    'id' => 1,
                    'name' => 'Rigel',
                    'url' => 'rigel.terratritium.com',
                    'type' => 'Beta'
                ],
                'Rigel' => [
                    'id' => 1,
                    'name' => 'Sirius',
                    'url' => 'sirius.terratritium.com',
                    'type' => 'Test'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error on list servers.'],
                Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
