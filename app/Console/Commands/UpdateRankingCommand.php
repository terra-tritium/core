<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class UpdateRankingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updated player rankings';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::table('ranking')->truncate();

            $players = DB::table('players as p')
                ->select('p.id', 'p.name', 'p.score', 'p.buildScore', 'p.attackScore', 'p.defenseScore', 
                'p.militaryScore', 'p.researchScore', 'p.aliance', 'a.name as alianceName')
                ->leftJoin('aliances as a','a.id','=','p.aliance')
                ->orderBy('score', 'desc')
                ->get();


            $count = 0;

            foreach ($players as $player) {
                $count++;
                DB::table('ranking')->insert([
                    'name' => $player->name,
                    'position' => $count,
                    'player' => $player->id,
                    'energy' => $this->calculateTotalEnergy($player->id),
                    'score' => $player->score,
                    'buildScore' => $player->buildScore,
                    'attackScore' => $player->attackScore,
                    'defenseScore' => $player->defenseScore,
                    'militaryScore' => $player->militaryScore,
                    'researchScore' => $player->researchScore,
                    'aliance' => $player->aliance,
                    'alianceName' => $player->alianceName

                ]);
            }
        } catch (\Exception $exception) {
            Log::error('Erro no agendamento: ' . $exception->getMessage());
        //    Notification::route('discord', 'terra-tritium')->notify(new ExceptionNotification($exception));

        }

    }

    protected function calculateTotalEnergy($playerId)
    {
        $totalEnergy = DB::table('planets')
            ->where('player', $playerId)
            ->sum('energy');

        return $totalEnergy;
    }
}
