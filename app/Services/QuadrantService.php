<?php

namespace App\Services;
use App\Models\Planet;

class QuadrantService{

    public function calcDistanceQuadrant( $code,$planet) {
       
        $planetFrom = Planet::where(['id' => $planet])->first();
        
        if(!$planetFrom){
            return 0 ;
        }

        $regionTo       =   substr($code,0,1);
        $positionTo     =   $planetFrom->position ;
        $quadrantTo     =  substr($code,1);
        $quadrantFrom   = substr($planetFrom->quadrant,1);

        $diffRegion = abs(ord($planetFrom->region) - ord($regionTo));
        $diffQuadrant = abs((int) $quadrantFrom - (int) $quadrantTo);
        $diffPosition = abs((int) $planetFrom->position - (int) $positionTo);

        $distante  =  ($diffRegion * 100) + ($diffQuadrant * 10) + $diffPosition;

        return  $distante;
    }
}