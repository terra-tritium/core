<?php

namespace App\Console\Commands;

use App\Models\Aliance;
use App\Models\Planet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class UpdateEnergyAlianceCommand extends Command
{
    protected $signature = 'energy:update';
    protected $description = 'Update energy field in aliances table';

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
                $planets = Planet::join('players', 'players.id', '=', 'planets.player')
                    ->where('players.aliance', $aliance->id)
                    ->get();

                $energySum = $planets->sum('energy');

                $aliance->energy = $energySum;
                $aliance->save();
            }

            $this->info('Energy field updated successfully.');
        } catch (\Exception $e) {
            Log::error('Erro no agendamento de energy aliance: ' . $e->getMessage());
        }

    }
}
