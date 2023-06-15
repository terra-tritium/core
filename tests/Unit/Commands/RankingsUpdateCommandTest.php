<?php

namespace Tests\Unit\Commands;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Console\Commands\RankingsUpdateCommand;
use App\Models\Player;
use App\Models\RankingPlayer;

class RankingsUpdateCommandTest extends TestCase
{
    //use RefreshDatabase;

    public function testHandle()
    {
        $player = Player::factory()->create();

        $this->app->call(RankingsUpdateCommand::class);

        $rankingPlayer = RankingPlayer::where('player', $player->id)->first();

        $this->assertEquals($player->name, $rankingPlayer->name);
        $this->assertEquals($player->energy, $rankingPlayer->energy);
        $this->assertEquals($player->score, $rankingPlayer->score);
        $this->assertEquals($player->buildScore, $rankingPlayer->buildScore);
        $this->assertEquals($player->attackScore, $rankingPlayer->attackScore);
        $this->assertEquals($player->defenseScore, $rankingPlayer->defenseScore);
        $this->assertEquals($player->militaryScore, $rankingPlayer->militaryScore);
        $this->assertEquals($player->researchScore, $rankingPlayer->researchScore);
    }
}
