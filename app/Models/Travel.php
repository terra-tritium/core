<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Planet;
use Illuminate\Support\Facades\DB;

class Travel extends Model
{
    use HasFactory;
    protected $table = 'travels';
    public $timestamps = false;

    /* ACTION */
    CONST ATTACK_FLEET = 1;
    CONST ATTACK_TROOP = 2;
    CONST DEFENSE_FLEET = 3;
    CONST DEFENSE_TROOP = 4;
    CONST TRANSPORT_RESOURCE = 5;
    CONST TRANSPORT_BUY = 6;
    CONST TRANSPORT_SELL = 7;
    CONST MISSION_EXPLORER = 8;
    CONST RETURN_FLEET = 9;
    CONST RETURN_TROOP = 10;
    CONST MISSION_SPIONAGE = 9;
    /* STATUS */
    CONST STATUS_ON_LOAD = 1;
    CONST STATUS_ON_GOING = 2;
    CONST STATUS_RETURN = 3;
    CONST STATUS_FINISHED = 4;
    CONST STATUS_CANCEL = 5;

    public function from(){
        return $this->belongsTo(Planet::class, 'from');
    }

    public function to(){
        return $this->belongsTo(Planet::class, 'to');
    }

    public function getTravelsData($from){
        return $this->select(
            'id',
            'start',
            'to',
            'troop',
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(start), '%d/%m/%Y %H:%i:%s') AS start_date"),
            DB::raw("DATE_FORMAT(FROM_UNIXTIME(arrival), '%d/%m/%Y %H:%i:%s') AS arrival_date"),
            DB::raw("TIMESTAMPDIFF(SECOND, FROM_UNIXTIME(start), FROM_UNIXTIME(arrival)) AS time_difference_seconds"),
            DB::raw("TIMESTAMPDIFF(MINUTE, FROM_UNIXTIME(start), FROM_UNIXTIME(arrival)) AS time_difference_minutes"),
            DB::raw("TIMESTAMPDIFF(SECOND, NOW(), FROM_UNIXTIME(arrival)) AS time_until_arrival_seconds"),
            DB::raw("CASE WHEN NOW() >= FROM_UNIXTIME(arrival) THEN 1 ELSE 0 END AS chegou")
        )
        ->where(['status'=>1, 'from'=>$from])
        ->get();
    }
}
