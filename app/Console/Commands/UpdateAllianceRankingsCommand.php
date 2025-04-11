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
            $aliances = Aliance::with(['players.planets'])->get();

            foreach ($aliances as $aliance) {
                $energy = 0;

                // Verifica se há jogadores antes de tentar somar a energia
                if ($aliance->players) {
                    $energy = $aliance->players->sum(function ($player) {
                        // Verifica se o jogador tem planetas antes de somar a energia
                        return $player->planets ? $player->planets->sum('energy') : 0;
                    });
                }

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
