<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
                'idRank' =>1
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
        /**Membros alianca 2*/
        $membrosAtivos = [20, 21, 22, 23];

        foreach ($membrosAtivos as $mA) {
            $player = Player::where('user', $mA)->first();

            DB::table('aliances_members')->insert([
                'id' => $mA,
                'player_id' => $player->id,
                'idAliance' => 2,
                'status' => 'A',
                'role' => 'member',
                'dateAdmission' => (new DateTime())->format('Y-m-d H:i:s'),
                'idRank' =>7
            ]);
           

            DB::table('players')->where('id', $player->id)->update([
                'aliance' => $mA
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
