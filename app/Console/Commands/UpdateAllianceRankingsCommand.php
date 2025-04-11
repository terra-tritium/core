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
            // Usando carregamento eager para as relações
            $aliances = Aliance::getSumScoresMembers();

            foreach ($aliances as $aliance) {
                
                $ranking = AlianceRanking::firstOrNew(['aliance' => $aliance->id]);
                $ranking->score = $aliance->score;
                $ranking->buildScore = $aliance->buildScore;
                $ranking->labScore = $aliance->researchScore;
                $ranking->attackScore = $aliance->attackScore;
                $ranking->defenseScore = $aliance->defenseScore;
                $ranking->warScore = $aliance->militaryScore;
                $ranking->tradeScore = 0;
                $ranking->save();
            }

            $this->info('Alliance rankings updated successfully.');
        } catch (\Exception $e) {
            Log::error('Erro no agendamento de ranking aliance: ' . $e->getMessage());
        }
    }

}
