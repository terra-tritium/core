<?php

namespace App\Console\Commands;

use App\Models\RotinaDBG;
use App\Models\Trading;
use App\Services\TradingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonitorarTrades extends Command
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
    protected $description = 'Veriricar as transações';

    /**
     * Execute the console command.
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
}
