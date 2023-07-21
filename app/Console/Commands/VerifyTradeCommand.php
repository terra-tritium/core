<?php

namespace App\Console\Commands;

use App\Models\RotinaDBG;
use App\Models\Trading;
use App\Services\TradingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class VerifyTradeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trade:verifytrades';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica as transações concluidas';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $tradeService = new TradingService(new Trading());
            $tradeService->verificaTradeConcluidoSafe();
            $rotina = new RotinaDBG();
            $rotina->save();
           
            
        } catch (\Exception $exception) {
            Log::error('Erro no agendamento: ' . $exception->getMessage());
        //    Notification::route('discord', 'terra-tritium')->notify(new ExceptionNotification($exception));

        }

    }
    //    * * * * *  cd /home/kleiton/Documentos/projetos/tritium/back/core  && php artisan trade:verifytrades && php artisan schedule:run >> /dev/null 2>&1


    // * * * * * cd /home/kleiton/Documentos/projetos/tritium/back/core    && php artisan schedule:run >> /dev/null 2>&1


    protected function calculateTotalEnergy($playerId)
    {
        // $totalEnergy = DB::table('planets')
        //     ->where('player', $playerId)
        //     ->sum('energy');

        // return $totalEnergy;
    }
}
