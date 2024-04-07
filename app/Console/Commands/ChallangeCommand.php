<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChallangeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'challange:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updated new Tritium Challange';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::table('planets')->update(['yellowTrit' => 1]);
        } catch (\Exception $exception) {
            Log::error('Erro ao executar starto do tritium challange: ' . $exception->getMessage());
        }

    }
}
 