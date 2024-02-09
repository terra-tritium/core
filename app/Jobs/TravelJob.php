<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Travel;
use App\Services\SpaceCombatService;

class TravelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $travel;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($travel)
    {
        $this->travel = $travel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $currentTravel = Travel::find($this->travel);
        if ($currentTravel) {
            $currentTravel->status = Travel::STATUS_FINISHED;
            $currentTravel->save();

            switch ($currentTravel->action) {
                case Travel::ATTACK_FLEET:
                    $spaceCombatService = new SpaceCombatService();
                    $spaceCombatService->createNewCombat($currentTravel);
                    break;
                case Travel::ATTACK_TROOP:
                    //$this->travelService->starCombatTravel($this->travel);
                    break;
                case Travel::DEFENSE_FLEET:
                    //$this->travelService->starCombatTravel($this->travel);
                    break;
                case Travel::DEFENSE_TROOP:
                    //$this->travelService->starCombatTravel($this->travel);
                    break;
                case Travel::TRANSPORT_RESOURCE:
                    //$this->travelService->starTransportResource($this->travel);
                    break;
                case Travel::TRANSPORT_BUY:
                    //$this->travelService->starTransportBuy($this->travel);
                    break;
                case Travel::TRANSPORT_SELL:
                    //$this->travelService->starTransportSell($this->travel);
                    break;
                case Travel::MISSION_EXPLORER:
                    //$this->travelService->starMissionExplorer($this->travel);
                    break;
            }
        }
    }
}
