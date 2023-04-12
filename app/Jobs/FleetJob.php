<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Fleet;
use App\Models\Production;

class FleetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $planet;
    private $units;
    private $player;
    private $production;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($planet, $player, $units, $production, $type)
    {
        $this->planet = $planet;
        $this->units = $units;
        $this->player = $player;
        $this->production = $production;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->units as $key => $unit) {

            $fleet = Fleet::where([["planet", $this->planet],["unit", $unit["id"]]])->first();
            $prod = Production::find($this->production);
            $prod->executed = true;
            
            if ($fleet) {
                $fleet->quantity += $unit["quantity"];
            } else {
                $fleet = new Fleet();
                $fleet->player = $this->player;
                $fleet->planet = $this->planet;
                $fleet->unit = $unit["id"];
                $fleet->quantity = $unit["quantity"];
            }
            $prod->save();
            $fleet->save();
        }
    }
}
