<?php

namespace App\Console\Commands;

use App\Models\Player;
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

        $multiplyFactor = 3;

        try {
            
            $planets = DB::table('planets as p')
                ->select('p.id', 'p.name', 'p.yellowTrit')
                ->orderBy('p.yellowTrit', 'desc')
                ->limit(5)
                ->get();

            DB::table('challanges')->insert([
                'start' => time(),
                'first' => $planets[0]->id,
                'second' => $planets[1]->id,
                'third' => $planets[2]->id,
                'fourth' => $planets[3]->id,
                'fifth' => $planets[4]->id,
                'firstScore' => $planets[0]->yellowTrit,
                'secondScore' => $planets[1]->yellowTrit,
                'thirdScore' => $planets[2]->yellowTrit,
                'fourthScore' => $planets[3]->yellowTrit,
                'fifthScore' => $planets[4]->yellowTrit
            ]);

            $planets = DB::table('planets as p')
                ->select('p.id', 'p.name', 'p.yellowTrit', 'p.player')
                ->where('p.yellowTrit', '>', 1)
                ->orderBy('p.yellowTrit', 'desc')
                ->limit(5)
                ->get();

            foreach ($planets as $planet) {
                $player = Player::find($planet->player);
                $player->tritium += ($planet->yellowTrit * $multiplyFactor);
                $player->save();
            }

            DB::table('planets')->update(['yellowTrit' => 1]);


        } catch (\Exception $exception) {
            Log::error('Erro ao executar starto do tritium challange: ' . $exception->getMessage());
        }

    }
}
 