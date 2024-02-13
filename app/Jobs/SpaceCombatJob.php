<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Combat;
use App\Services\CombatService;

class SpaceCombatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $spaceCombatService;
    private $combatId;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($spaceCombatService, $combatId)
    {
        $this->spaceCombatService = $spaceCombatService;
        $this->combatId = $combatId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->spaceCombatService->excuteStage($this->combatId);
    }
}
