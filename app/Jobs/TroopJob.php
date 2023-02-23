<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Troop;

class TroopJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $planet;
    private $units;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($planet, $units)
    {
        $this->planet = $planet;
        $this->units = $units;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->units as $key => $unit) {
            $troop = Troop::where([["planet", $this->planet],["unit", $unit->id]])->first();
            if (array_key_exists("quantity", $unit)) {
                if ($troop) {
                    $troop->quantity += $unit["quantity"];
                    $troop->save();
                } else {
                    $troop = new Troop();
                    $troop->planet = $this->planet;
                    $troop->unit = $unit["id"];
                    $troop->quantity = $unit["quantity"];
                    $troop->save();
                }
            }
        }
    }
}
