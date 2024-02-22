<?php

namespace App\Services;
use App\Models\Planet;
use App\Services\PlanetService;

class QuadrantService{

    protected $planetServico;

    public function __construct(PlanetService $planetServico)
    {
        $this->planetServico =  $planetServico;
    }

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


    public function calcDistancePlanets($player, $origin,$destiny) {

        ## Fazer o calculo baseado na velocidade e/ou itens do jogo;
        $timeTravel = $this->planetServico->calculeDistance($origin,$destiny);

        return $timeTravel;
    }

}
