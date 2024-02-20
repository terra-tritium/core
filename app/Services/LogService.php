<?php

namespace App\Services;

use App\Models\Logbook;

class LogService
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
