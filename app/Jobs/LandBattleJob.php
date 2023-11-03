<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Battle;
use App\Services\LandBattleService;

class LandBattleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $landBattleService;
    private $battleId;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($landBattleService, $battleId)
    {
        $this->landBattleService = $landBattleService;
        $this->battleId = $battleId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->landBattleService->runStage($this->battleId, 'attack');
        $this->landBattleService->runStage($this->battleId, 'defense');
    }
}
