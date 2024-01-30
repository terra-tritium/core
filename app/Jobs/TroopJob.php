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
use Illuminate\Support\Facades\Log;

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
    
        // Log::info('Tipo de $this->units:', ['tipo' => $this->units['id']]);
        // Log::info('Tipo de $this->quantidade:', ['quantidade' => $this->units['quantity']]);
        $troop = Troop::where([["planet", $this->planet],["unit", $this->units["id"]]])->first();
        $prod = Production::find($this->production);

        // Log::info("troop", ['troop player' => $troop->player]);
        // Log::info('producao ',["producao"=>$prod]);

        if($troop){
            $troop->quantity += $this->units["quantity"];
            if($troop->save())
                $prod->delete();
        }else{
            $troop = new Troop();
            $troop->player = $this->player;
            $troop->planet = $this->planet;
            $troop->unit = $this->units["id"];
            $troop->quantity = $this->units["quantity"];
            if($troop->save()){
                $prod->delete();
            }
        }
        return;
    }
}
