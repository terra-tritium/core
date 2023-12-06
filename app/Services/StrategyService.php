<?php

namespace App\Services;

use App\Models\Strategy;

class StrategyService {

    public function getStrategies($planet){
        $strategy = new Strategy();
        return $strategy->getStrategies($planet);
    }

}