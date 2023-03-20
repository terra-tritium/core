<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Battle;
use App\Services\BattleService;

class BattleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $battleService;
    private $battleId;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($battleService, $battleId)
    {
        $this->battleService = $battleService;
        $this->battleId = $battleId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->battleService->calculateStage($this->battleId);
    }
}
