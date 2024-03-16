<?php

namespace App\Services;

use App\Models\Building;
use App\Models\Espionage;
use App\Models\Fleet;
use App\Models\Planet;
use App\Models\Troop;
use Carbon\Carbon;

class EspionadeService
{

    public function finallySpy($travel)
    {
        $spyModel = Espionage::where('travel',$travel->id)->first();

        $spyModel->success = $this->calculionProbabilisticSuccess($spyModel);

        if($spyModel->success)
        {
            $planetModel = Planet::findOrFail($spyModel->planet);
            switch($spyModel->typeSpy)
            {
                case Espionage::TYPE_SPY_FLEET:
                    $fleets = Fleet::leftJoin('ships','ships.id','=','fleet.id')
                                    ->select('fleet.*','ships.name')
                                    ->where('planet',$spyModel->planet)
                                    ->get();
                    $spyModel->fleet =  json_encode($fleets);
                    break;

                case Espionage::TYPE_SPY_RESEARCH:
                    $building =  Building::where('planet',$spyModel->planet)->get();
                    $spyModel->metal     = $planetModel->metal     ;
                    $spyModel->crystal   = $planetModel->crystal   ;
                    $spyModel->uranium   = $planetModel->uranium   ;
                    break;

                case Espionage::TYPE_SPY_RESOURCE:
                    $spyModel->metal     = $planetModel->metal     ;
                    $spyModel->crystal   = $planetModel->crystal   ;
                    $spyModel->uranium   = $planetModel->uranium   ;
                    break;

                case Espionage::TYPE_SPY_TROOP:
                    $troops = Troop::leftJoin('units','units.id','=','troop.id')
                                    ->select('troop.*','units.name')
                                    ->where('planet',$spyModel->planet)
                                    ->get();
                    $spyModel->troop =  json_encode($troops);
                    break;
            }
        }

        $spyModel->end_date = Carbon::now();
        $spyModel->finished = true;
        $spyModel->save();
    }

    private function calculionProbabilisticSuccess($spyModel)
    {
        $number = mt_rand(1000, 9999) % 2;
        if($number == 0)
        {
            return true;
        }else{
            return false;
        }
    }

    public function list($player)
    {
        $espionade =  Espionage::leftJoin('travels','travels.id','=','espionage.travel')
                            ->leftJoin('planets','planets.id','=','espionage.planet')
                            ->where('spy',$player->id)
                            ->select(
                                    'espionage.*'
                                    ,'travels.id'
                                    ,'travels.arrival'
                                    ,'travels.start'
                                    ,'travels.status'
                                    ,'planets.name'
                                    ,'planets.quadrant'
                            )
                            ->get();
        return $espionade;
    }
}
