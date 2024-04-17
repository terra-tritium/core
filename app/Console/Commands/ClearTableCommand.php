<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ClearTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear-table:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpar as tabelas de log e travel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            DB::delete('delete from espionage where created_at < DATE_ADD(now(), INTERVAL -10 DAY)');

            DB::delete('delete from travels where createdAt < DATE_ADD(now(), INTERVAL -10 DAY)');

            DB::delete('delete from logbook  where  date < DATE_ADD(now(), INTERVAL -10 DAY)');

        } catch (\Exception $exception) {
            Log::error('Erro ao limpar as tabelas de log e travel: ' . $exception->getMessage());
        }

    }
}
