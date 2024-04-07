<?php

namespace App\Jobs;

use App\Services\ChallangeService;
use App\Models\Travel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChallangeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $travel;
    private $return;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($travel, $return = false)
    {
        $this->travel = $travel;
        $this->return = $return;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $challangeService =  new ChallangeService();
            if ($this->return) {
                $this->travel->status = Travel::STATUS_FINISHED;
                $this->travel->save();
            } else {
                $challangeService->executeMission($this->travel);
            }
        } catch (\Exception $e) {
           
        }
    }
}
