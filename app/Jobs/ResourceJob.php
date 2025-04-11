<?php

namespace App\Jobs;

use App\Models\Planet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Travel;
use App\Models\Player;
use App\Models\ProcessJob;
use Illuminate\Support\Facades\Log;

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
    private $player;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($travelService,$player,$origin, $target, $metal, $uranium, $crystal, $transportShips)
    {
        $this->player = $player;
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
        Log::info("executou o carregamento");
        $travel = new Travel();
        $travel->metal = $this->metal;
        $travel->uranium = $this->uranium;
        $travel->crystal =$this->crystal;
        $travel->transportShips = $this->transportShips ;
        $travel->action = Travel::TRANSPORT_RESOURCE;
        $travel->from = $this->origin;
        $travel->to = $this->target;
        $travel->strategy = 1;

        ProcessJob::where(['planet' => $this->origin,'type' => ProcessJob::TYPE_CARRYING])->delete();
        try{
            $this->travelService->start($this->player, $travel);
        } catch (\Exception $e)
        {
            Log::error($e->getMessage());
            $planetOrigim = Planet::findOrFail($this->origin);
            $planetOrigim->metal    += $this->metal;
            $planetOrigim->uranium  += $this->uranium;
            $planetOrigim->crystal  += $this->crystal;
            $planetOrigim->save();

            $playerOrigim = Player::findOrFail($planetOrigim->player);
            $playerOrigim->transportShips += $this->transportShips ;
            $playerOrigim->save();
        }
    }
}
