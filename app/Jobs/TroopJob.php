<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Troop;
use App\Models\Production;

class TroopJob implements ShouldQueue
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
    public function __construct($planet, $player, $units, $production)
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

            $troop = Troop::where([["planet", $this->planet],["unit", $unit["id"]]])->first();
            $prod = Production::find($this->production);
            $prod->executed = true;
            
            if ($troop) {
                $troop->quantity += $unit["quantity"];
            } else {
                $troop = new Troop();
                $troop->player = $this->player;
                $troop->planet = $this->planet;
                $troop->unit = $unit["id"];
                $troop->quantity = $unit["quantity"];
            }
            $prod->save();
            $troop->save();
        }
    }
}
