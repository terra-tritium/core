<?php

namespace App\Services;

use App\Models\Troop;
use App\Models\Player;
use App\Models\Unit;

use App\Services\PlayerService;

class TroopService
{
    private $playerService;

    public function __construct() {
        $this->playerService = new PlayerService();
    }

    public function build ($address, $planet, $units) {
        if ($this->hasFunds($units, $address)) {
            foreach ($units as $key => $unit) {
                $troop = Troop::where([["unit", $unit->id], ["planet", $planet]])->get();
                if (count($troop) > 0) {
                    $troop->quantity += $unit->quantity;
                } else {
                    $troop = new Troop();
                    $troop->planet = $planet;
                    $troop->address = $address;
                    $troop->unit = $unit->id;
                    $troop->quantity = $unit->quantity;
                }
                $troop->save();
            }
        } else {
            return "No suficients Funds";
        }
    }

    private function hasFunds($units, $address) {
        $p1 = Player::where("address", $address)->findOrFail();
        foreach ($units as $key => $unit) {
            $unitModel = Unit::find($unit->id);
            if (!$this->playerService->enoughBalance($p1, ($unitModel->metal * $unit->quantity), 1)){
                return false;
            }
            if (!$this->playerService->enoughBalance($p1, ($unitModel->deuterium * $unit->quantity), 2)){
                return false;
            }
            if (!$this->playerService->enoughBalance($p1, ($unitModel->crystal * $unit->quantity), 3)){
                return false;
            }
        }
        return true;
    }
}