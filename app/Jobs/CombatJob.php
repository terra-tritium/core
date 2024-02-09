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

class CombatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $combatService;
    private $combatId;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($combatService, $combatId)
    {
        $this->combatService = $combatService;
        $this->combatId = $combatId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->combatService->calculateStage($this->combatId);
    }
}
