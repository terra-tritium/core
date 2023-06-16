<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseDropCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:drop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop current database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $connection = DB::connection();

            $connection->select("SELECT 1 FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '".env('DB_DATABASE')."'");
            
            DB::statement('DROP DATABASE ' . env('DB_DATABASE'));

        } catch (\Exception $exception) {
            Log::error('Erro on drop database: ' . $exception->getMessage());
        }

    }
}
