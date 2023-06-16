<?php

namespace App\Console\Commands;

use App\Models\Aliance;
use App\Models\AlianceRanking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateAllianceRankingsCommand extends Command
{

    protected $signature = 'aliances-rankings:update';

    protected $description = 'Update alliance rankings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $aliances = Aliance::all();

            foreach ($aliances as $aliance) {
                $energy = $aliance->players->sum(function ($player) {
                    return $player->planets->sum('energy');
                });

                $ranking = AlianceRanking::firstOrNew(['aliance' => $aliance->id]);
                $ranking->energy = $energy;
                $ranking->score = $aliance->score;
                $ranking->buildScore = $aliance->buildScore;
                $ranking->labScore = $aliance->labScore;
                $ranking->tradeScore = $aliance->tradeScore;
                $ranking->attackScore = $aliance->attackScore;
                $ranking->defenseScore = $aliance->defenseScore;
                $ranking->warScore = $aliance->warScore;
                $ranking->save();
            }

            $this->info('Alliance rankings updated successfully.');
        } catch (\Exception $e) {
            Log::error('Erro no agendamento de ranking aliance: ' . $e->getMessage());
        }
    }
}
