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
    private $travelService;
    private $back;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($travelService, $travel, $back)
    {
        $this->travel = $travel;
        $this->travelService  = $travelService;
        $this->back = $back;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $currentTravel = Travel::where('id',$this->travel)->whereNotIn('status',[Travel::STATUS_CANCEL])->first();
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
                    if($this->back){
                        $this->travelService->arrivedTransportOrigin($this->travel);
                    }else{
                        $this->travelService->arrivedTransportResource($this->travel);
                    }
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
                case Travel::RETURN_FLEET:
                    $this->travelService->landingOfShips($currentTravel);
                    break;
                case Travel::MISSION_COLONIZATION:
                    $this->travelService->missionColonization($currentTravel);
                    break;
            }
        }
    }
}
