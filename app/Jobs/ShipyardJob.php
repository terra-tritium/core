<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Shipyard;
use App\Models\Production;

class ShipyardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $planet;
    private $unitsShipyard;
    private $player;
    private $production;

    public function __construct($planet, $player, $unitsShipyard, $production)
    {
        $this->planet = $planet;
        $this->unitsShipyard = $unitsShipyard;
        $this->player = $player;
        $this->production = $production;
    }

    public function handle()
    {
        foreach ($this->unitsShipyard as $unit) {
            $shipyard = Shipyard::where([["planet", $this->planet],["unit", $unit["id"]]])->first();
            $prod = Production::find($this->production);

            if ($shipyard) {
                $shipyard->quantity += $unit["quantity"];
            } else {
                $shipyard = new Shipyard();
                $shipyard->player = $this->player;
                $shipyard->planet = $this->planet;
                $shipyard->unit = $unit["id"];
                $shipyard->quantity = $unit["quantity"];
            }

            $shipyard->save();
            $prod->delete();
        }
    }
}
