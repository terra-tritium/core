<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Models\Player;
use App\Models\Ranking;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->command('rankings:update')->everyThirtyMinutes();


        # Ranking
        $schedule->call(function () {

            $players = Player::all();

            foreach($players as $p) {
                $ranking = new Ranking();
                $ranking->name = $p->name;
                $ranking->player = $p->player;
                $ranking->energy = $p->energy;
                $ranking->score = $p->score;
                $ranking->buildScore = $p->buildScore;
                $ranking->attackScore = $p->attackScore;
                $ranking->defenseScore = $p->defenseScore;
                $ranking->militaryScore = $p->militaryScore;
                $ranking->save();
            }
        })->everyThirtyMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
