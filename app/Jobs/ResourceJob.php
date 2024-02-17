<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Travel;
use App\Models\Logbook;

class ResourceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $travelService;
    private $origin;
    private $target;
    private $metal;
    private $uranium;
    private $crystal;
    private $transportShips;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($travelService,$origin, $target, $metal, $uranium, $crystal, $transportShips)
    {
        $this->origin = $origin;
        $this->target = $target;
        $this->metal = $metal;
        $this->uranium = $uranium;
        $this->crystal = $crystal;
        $this->transportShips = $transportShips;
        $this->travelService = $travelService ;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $travel = new Travel();
        $travel->metal = $this->metal;
        $travel->uranium = $this->uranium;
        $travel->crystal =$this->crystal;
        $travel->transportShips = $this->transportShips ; 
        $travel->action = Travel::TRANSPORT_RESOURCE; 
        $travel->from = $this->origin; 
        $travel->to = $this->target; 
        $travel->strategy = 1; 

        $this->travelService->start($this->origin,$travel);
    }
}
