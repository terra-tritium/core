<?php

namespace App\Http\Controllers;

use App\Models\Logbook;

class LogbookController extends Controller
{

    public function notify($playerId, $text, $type)
    {
        $log = new Logbook();
        $log->player = $playerId;
        $log->text = $text;
        $log->type = $type;
        $log->save();
    }
}
