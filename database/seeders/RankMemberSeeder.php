<?php

namespace Database\Seeders;
//subir
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RankMemberSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rank_member ')->insert([
            'id'=>1,
            'level' => 12,
            "rankName" => "Alliance Founder",
            "limit" => 1,
            "description" => "Dissolver/Mudar nome e Delegar Cargos. Adiciona/expulsa novos membros",
            "visible" => false
        ]);

        DB::table('rank_member')->insert([
            'id'=>2,
            'level' => 10,
            "rankName" => "General",
            "limit" => 1,
            "description" => "Delegar cargos inferiores (exceto alterar fundador). Visão de toda a tropa/tropa da Aliança (Qtd e Localização/Ação); Ter poder para parar ataque.",
            "visible" => true
        ]);

        DB::table('rank_member')->insert([
            'id'=>3,
            'level' => 8,
            'limit' => 1,
            "rankName" => "Fleet Captain",
            "limit" => 1,
            "description" => "Visão de toda a frota da Aliança (Qtd e Localização/Ação)",
            "visible" => true
        ]);

        DB::table('rank_member')->insert([
            'id'=>4,
            'level' => 8,
            "rankName" => "Troop Captain",
            "limit" => 1,
            "description" => "Visão de toda a tropa/ da Aliança (Qtd e Localização/Ação)",
            "visible" => true
        ]);

        DB::table('rank_member')->insert([
            'id'=>5,
            'level' => 6,
            "rankName" => "Diplomat",
            "limit" => 1,
            "description" => "Adiciona novos membros, expulsa membros, responde mensagens",
            "visible" => true
        ]);

        DB::table('rank_member')->insert([
            'id'=>6,
            'level' => 4,
            "rankName" => "Corporal",
            "limit" => null,
            "description" => "nada",
            "visible" => true
        ]);

        DB::table('rank_member')->insert([
            'id'=>7,
            'level' => 2,
            "rankName" => "Soldier",
            "limit" => null,
            "description" => "nada",
            "visible" => true
        ]);
    }
}
