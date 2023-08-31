<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\User;
use App\Services\PlayerService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::inRandomOrder()->limit(150)->get();
        $users->each(function ($user) {
            $PlayerService = new PlayerService();
            // Player::factory()->create([
            //     'user' => $user->id,
            //     'name' => $user->name
            // ]);
            $player = new Player();
            $player->name = $user->name;
            $player->country = rand(1, 6);
            $player->user = $user->id;
           
            $PlayerService->register($player);
        });
        DB::table('planets')->update(['name' => DB::raw("CONCAT(name, id)")]);
    }
}
