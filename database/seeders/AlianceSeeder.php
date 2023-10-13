<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Aliance;
use App\Models\Planet;
use App\Models\Player;
use DateTime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AlianceSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /**Criando aliancas e colocando os fundadores */
        $idFounders = [2, 3, 4, 5];
        foreach ($idFounders as $id) {
            $player = Player::where('user', $id)->first();
            if(!$player) continue;
            DB::table('aliances')->insert([
                'id' => $id,
                'description' => 'Descrição da Aliança, o fundador é o ' . $player->name ?? " alguém",
                'name' => 'Os Galáticos ' . $player->name,
                'logo' => 'logo_fill_01' . $id . '.jpg',
                'energy' => 0,
                'score' => 0,
                'buildScore' => 0,
                'labScore' => 0,
                'tradeScore' => 0,
                'attackScore' => 0,
                'defenseScore' => 0,
                'warScore' => 0,
                'founder' => $player->id,
                'status' => 'F',

            ]);

            DB::table('aliances_members')->insert([
                'player_id' => $player->id,
                'idAliance' => $id,
                'status' => 'A',
                'role' => 'founder',
                'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s'),
                'idRank' => 1
            ]);
            DB::table('players')->where('id', $player->id)->update([
                'aliance' => $id
            ]);
        }
        /**Membros alianca 2*/
        $membrosAtivos = [6, 7, 8, 9, 10];
        $id = 3000;
        foreach ($membrosAtivos as $mA) {
            $player = Player::where('user', $mA)->first();
            $aliance = Aliance::find(2);
            if (!$player || !$aliance) continue;

            DB::table('aliances_members')->insert([
                'id' => $id,
                'player_id' => $player->id,
                'idAliance' => 2,
                'status' => 'A',
                'role' => 'member',
                'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s'),
                'idRank' => 7
            ]);
            DB::table('players')->where('id', $player->id)->update([
                'aliance' => 2
            ]);
            $id++;
        }

        /**Membros alianca 3*/
        $membrosAtivos = [11, 12, 13];
        $id = 1000;
        foreach ($membrosAtivos as $mA) {
            $player = Player::where('user', $mA)->first();
            $aliance = Aliance::find(3);
            if (!$player || !$aliance) continue;
            DB::table('aliances_members')->insert([
                'id' => $id,
                'player_id' => $player->id,
                'idAliance' => 3,
                'status' => 'A',
                'role' => 'member',
                'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s'),
                'idRank' => 6
            ]);
            DB::table('players')->where('id', $player->id)->update([
                'aliance' => 3
            ]);
            $id++;
        }


        $membrosAtivos = [14, 15, 16];
        $id = 2000;
        foreach ($membrosAtivos as $mA) {
            $player = Player::where('user', $mA)->first();
            $aliance = Aliance::find(4);
            if (!$player || !$aliance) continue;
            DB::table('aliances_members')->insert([
                'id' => $id,
                'player_id' => $player->id,
                'idAliance' => 4,
                'status' => 'A',
                'role' => 'member',
                'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s'),
                'idRank' => 6
            ]);
            DB::table('players')->where('id', $player->id)->update([
                'aliance' => 4
            ]);
            $id++;
        }

        $membrosAtivos = [17, 18, 19];
        $id = 4000;
        foreach ($membrosAtivos as $mA) {
            $player = Player::where('user', $mA)->first();
            $aliance = Aliance::find(5);
            if (!$player || !$aliance) continue;
            DB::table('aliances_members')->insert([
                'id' => $id,
                'player_id' => $player->id,
                'idAliance' => 5,
                'status' => 'A',
                'role' => 'member',
                'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s'),
                'idRank' => 6
            ]);
            DB::table('players')->where('id', $player->id)->update([
                'aliance' => 5
            ]);
            $id++;
        }

        //diplomata
        $player = Player::where('user', 20)->first();
        $aliance = Aliance::find(2);
        if (!$player || !$aliance) {
            DB::table('aliances_members')->insert([
                'id' => 1050,
                'player_id' => $player->id,
                'idAliance' => 2,
                'status' => 'A',
                'role' => 'member',
                'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s'),
                'idRank' => 5
            ]);
            DB::table('players')->where('id', $player->id)->update([
                'aliance' => 2
            ]);
        }
        //diplomata
        $player = Player::where('user', 21)->first();
        $aliance = Aliance::find(3);
        if (!$player || !$aliance) {
            DB::table('aliances_members')->insert([
                'id' => 1060,
                'player_id' => $player->id ?? rand(1000,2000),
                'idAliance' => 3,
                'status' => 'A',
                'role' => 'member',
                'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s'),
                'idRank' => 5
            ]);
            DB::table('players')->where('id', $player->id)->update([
                'aliance' => 3
            ]);
        }

        //diplomata
        $player = Player::where('user', 22)->first();
        $aliance = Aliance::find(4);
        if (!$player || !$aliance) {
            DB::table('aliances_members')->insert([
                'id' => 1070,
                'player_id' => $player->id,
                'idAliance' => 4,
                'status' => 'A',
                'role' => 'member',
                'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s'),
                'idRank' => 5
            ]);
            DB::table('players')->where('id', $player->id)->update([
                'aliance' => 4
            ]);
        }

        //diplomata
        $player = Player::where('user', 23)->first();
        $aliance = Aliance::find(5);
        if (!$player || !$aliance) {
            DB::table('aliances_members')->insert([
                'id' => 1090,
                'player_id' => $player->id,
                'idAliance' => 5,
                'status' => 'A',
                'role' => 'member',
                'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s'),
                'idRank' => 5
            ]);
            DB::table('players')->where('id', $player->id)->update([
                'aliance' => 5
            ]);
        }


        $player = Player::where('user', 27)->first();
        $aliance = Aliance::find(5);
        if (!$player || !$aliance) {
            DB::table('aliances_members')->insert([
                'id' => 1090,
                'player_id' => $player->id,
                'idAliance' => 5,
                'status' => 'A',
                'role' => 'member',
                'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s'),
                'idRank' => 5
            ]);
            DB::table('players')->where('id', $player->id)->update([
                'aliance' => 5
            ]);
        }
        $player = Player::where('user', 24)->first();
        $aliance = Aliance::find(2);
        if (!$player || !$aliance) {
            DB::table('aliances_members')->insert([
                'id' => 2090,
                'player_id' => $player->id,
                'idAliance' => 2,
                'status' => 'A',
                'role' => 'member',
                'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s'),
                'idRank' => 2
            ]);
            DB::table('players')->where('id', $player->id)->update([
                'aliance' => 2
            ]);
        }
        $player = Player::where('user', 25)->first();
        $aliance = Aliance::find(3);
        if (!$player || !$aliance) {
            DB::table('aliances_members')->insert([
                'id' => 2080,
                'player_id' => $player->id,
                'idAliance' => 3,
                'status' => 'A',
                'role' => 'member',
                'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s'),
                'idRank' => 2
            ]);
            DB::table('players')->where('id', $player->id)->update([
                'aliance' => 3
            ]);
        }
        $player = Player::where('user', 26)->first();
        $aliance = Aliance::find(4);
        if (!$player || !$aliance) {
            DB::table('aliances_members')->insert([
                'id' => 2090,
                'player_id' => $player->id,
                'idAliance' => 4,
                'status' => 'A',
                'role' => 'member',
                'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s'),
                'idRank' => 2
            ]);
            DB::table('players')->where('id', $player->id)->update([
                'aliance' => 4
            ]);
        }


        //     DB::table('players')->where('id', $player->id)->update([
        //         'aliance' => $mA
        //     ]);

        //     $planet = Planet::where('player', $player->id)->first();
        //     DB::table('buildings')->insert([
        //         'build' => 1,
        //         'planet' => $planet->id,
        //         'level' => 1,
        //         'slot' => 10,
        //         'workers' => 1,
        //         'ready' => 1000
        //     ]);
        //     DB::table('buildings')->insert([
        //         'build' => 14,
        //         'planet' => $planet->id,
        //         'level' => 1,
        //         'slot' => 11,
        //         'workers' => 1,
        //         'ready' => 1000
        //     ]);



        /**Membros alianca 3
           $membrosAtivos3 = [30, 31, 32];

           foreach ($membrosAtivos3 as $mA3) {
               $player = Player::where('user', $mA3)->first();
               $sql = "INSERT INTO log (logTxt) VALUES (?)";
               DB::insert($sql, [$player->name ?? 'teste'.' - '.$player->id ?? 'sem id']);
   
               /*DB::table('aliances_members')->insert([
                    'id' => $mA3,
                   'player_id' => $player->id,
                   'idAliance' => 3,
                   'status' => 'A',
                   'role' => 'member',
                   'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s')
               ]);
               DB::table('players')->where('id', $player->id)->update([
                   'aliance' => $id
               ]);
   
               $planet = Planet::where('player', $player->id)->first();
               DB::table('buildings')->insert([
                   'build' => 1,
                   'planet' => $planet->id,
                   'level' => 1,
                   'slot' => 10,
                   'workers' => 1,
                   'ready' => 1000
               ]);
               DB::table('buildings')->insert([
                   'build' => 14,
                   'planet' => $planet->id,
                   'level' => 1,
                   'slot' => 11,
                   'workers' => 1,
                   'ready' => 1000
               ]);
           }*/

        /**Membros alianca 4
           $membrosAtivos = [40, 41, 42];

           foreach ($membrosAtivos as $mA) {
               $player = Player::where('user', $mA)->first();
               DB::table('aliances_members')->insert([
                   'player_id' => $player->id,
                   'idAliance' => 4,
                   'status' => 'A',
                   'role' => 'member',
                   'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s')
               ]);
               DB::table('players')->where('id', $player->id)->update([
                   'aliance' => $id
               ]);
   
               $planet = Planet::where('player', $player->id)->first();
               DB::table('buildings')->insert([
                   'build' => 1,
                   'planet' => $planet->id,
                   'level' => 1,
                   'slot' => 10,
                   'workers' => 1,
                   'ready' => 1000
               ]);
               DB::table('buildings')->insert([
                   'build' => 14,
                   'planet' => $planet->id,
                   'level' => 1,
                   'slot' => 11,
                   'workers' => 1,
                   'ready' => 1000
               ]);
           }

           /**Membros alianca 5*
           $membrosAtivos = [50, 51, 22];

           foreach ($membrosAtivos as $mA) {
               $player = Player::where('user', $mA)->first();
               DB::table('aliances_members')->insert([
                   'player_id' => $player->id,
                   'idAliance' => 5,
                   'status' => 'A',
                   'role' => 'member',
                   'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s')
               ]);
               DB::table('players')->where('id', $player->id)->update([
                   'aliance' => $id
               ]);
   
               $planet = Planet::where('player', $player->id)->first();
               DB::table('buildings')->insert([
                   'build' => 1,
                   'planet' => $planet->id,
                   'level' => 1,
                   'slot' => 10,
                   'workers' => 1,
                   'ready' => 1000
               ]);
               DB::table('buildings')->insert([
                   'build' => 14,
                   'planet' => $planet->id,
                   'level' => 1,
                   'slot' => 11,
                   'workers' => 1,
                   'ready' => 1000
               ]);
           }


           Membros Pendente alianca 2 
           $membrosPendente = [24, 25, 26];

           foreach ($membrosPendente as $mP) {
               $player = Player::where('user', $mP)->first();
               DB::table('aliances_members')->insert([
                   'player_id' => $player->id,
                   'idAliance' => 2,
                   'status' => 'P',
                   'role' => 'member',
                   'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s')
               ]);
   
               $planet = Planet::where('player', $player->id)->first();
               DB::table('buildings')->insert([
                   'build' => 1,
                   'planet' => $planet->id,
                   'level' => 1,
                   'slot' => 10,
                   'workers' => 1,
                   'ready' => 1000
               ]);
               DB::table('buildings')->insert([
                   'build' => 14,
                   'planet' => $planet->id,
                   'level' => 1,
                   'slot' => 11,
                   'workers' => 1,
                   'ready' => 1000
               ]);
           }

           /**Membros Pendente alianca 3 
           $membrosPendente = [35, 36, 37];

           foreach ($membrosPendente as $mP) {
               $player = Player::where('user', $mP)->first();
               DB::table('aliances_members')->insert([
                   'player_id' => $player->id,
                   'idAliance' => 3,
                   'status' => 'P',
                   'role' => 'member',
                   'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s')
               ]);
   
               $planet = Planet::where('player', $player->id)->first();
               DB::table('buildings')->insert([
                   'build' => 1,
                   'planet' => $planet->id,
                   'level' => 1,
                   'slot' => 10,
                   'workers' => 1,
                   'ready' => 1000
               ]);
               DB::table('buildings')->insert([
                   'build' => 14,
                   'planet' => $planet->id,
                   'level' => 1,
                   'slot' => 11,
                   'workers' => 1,
                   'ready' => 1000
               ]);
           }

            /**Membros Pendente alianca 4 
            $membrosPendente = [45, 46, 47];

            foreach ($membrosPendente as $mP) {
                $player = Player::where('user', $mP)->first();
                DB::table('aliances_members')->insert([
                    'player_id' => $player->id,
                    'idAliance' => 4,
                    'status' => 'P',
                    'role' => 'member',
                    'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s')
                ]);
    
                $planet = Planet::where('player', $player->id)->first();
                DB::table('buildings')->insert([
                    'build' => 1,
                    'planet' => $planet->id,
                    'level' => 1,
                    'slot' => 10,
                    'workers' => 1,
                    'ready' => 1000
                ]);
                DB::table('buildings')->insert([
                    'build' => 14,
                    'planet' => $planet->id,
                    'level' => 1,
                    'slot' => 11,
                    'workers' => 1,
                    'ready' => 1000
                ]);
            }


             /**Membros Pendente alianca 5 
           $membrosPendente = [55, 56, 57];

           foreach ($membrosPendente as $mP) {
               $player = Player::where('user', $mP)->first();
               DB::table('aliances_members')->insert([
                   'player_id' => $player->id,
                   'idAliance' => 5,
                   'status' => 'P',
                   'role' => 'member',
                   'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s')
               ]);
   
               $planet = Planet::where('player', $player->id)->first();
               DB::table('buildings')->insert([
                   'build' => 1,
                   'planet' => $planet->id,
                   'level' => 1,
                   'slot' => 10,
                   'workers' => 1,
                   'ready' => 1000
               ]);
               DB::table('buildings')->insert([
                   'build' => 14,
                   'planet' => $planet->id,
                   'level' => 1,
                   'slot' => 11,
                   'workers' => 1,
                   'ready' => 1000
               ]);
           }
         */
    }
}
