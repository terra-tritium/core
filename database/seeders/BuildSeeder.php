<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('builds')->insert([
            "name" => "Colonization",
            "code" => 1,
            "image" => "build-01.png",
            "description" => "O módulo de colonização é um sonho flutuante, uma casa voadora que leva a humanidade para além de seus limites. É um navio da esperança, carregando uma carga valiosa de vida humana e recursos para a próxima fronteira do universo. É uma mistura de engenharia e arte, projetada para sobreviver em ambientes hostis e fornecer conforto e segurança para aqueles que o habitam.",
            "maxLevel" => 52,
            "effect" => "A cada nível de expanção permite a colonização de um novo planeta"
        ]);

        DB::table('builds')->insert([
            "name" => "Energy Collector",
            "code" => 2,
            "image" => "build-02.png",
            "description" => "O núcleo possui baterias e coletores, a bateria armazena energia, quanto mais núcleos maior poder de armazenagem da bateria. Os coletores são responsáveis por coletar energia e armazenar nas baterias.",
            "maxLevel" => 52,
            "effect" => "A cada nível de expanção permite a colonização de um novo planeta"
        ]);

        DB::table('builds')->insert([
            "name" => "Humanoid Factory",
            "code" => 3,
            "image" => "build-03.png",
            "description" => "A fábrica de robôs humanoides é uma sinfonia de tecnologia e criatividade, onde a inteligência artificial e a engenharia se unem para criar máquinas surpreendentes que imitam a forma humana. É uma obra de arte mecânica, onde a ciência e a arte se complementam para produzir robôs humanoides capazes de realizar tarefas complexas e interagir com o mundo ao nosso redor. É a visão de um futuro onde a tecnologia e a humanidade trabalham juntas para melhorar a vida e a sociedade.",
            "maxLevel" => 52,
            "effect" => "A cada nível de expanção permite a colonização de um novo planeta"
        ]);

        DB::table('builds')->insert([
            "name" => "Metal Mining",
            "code" => 4,
            "image" => "build-04.png",
            "description" => "Máquina de mineração de metal, a cada nível aumenta a capacidade de minerar metal, essa máquina exige energia como custo para manter o funcionamento. O metal é um recurso básico para construção de edifícios, máquinas e naves",
            "maxLevel" => 52,
            "effect" => "A cada nível de expanção permite a colonização de um novo planeta"
        ]);

        DB::table('builds')->insert([
            "name" => "Uranium Mining",
            "code" => 5,
            "image" => "build-05.png",
            "description" => "Máquina de mineração de uranium, a cada nível aumenta a capacidade de minerar uranium, essa máquina exige energia como custo para manter o funcionamento. O uranium é um recurso nobre usado nas construções, máquinas e naves específicas.",
            "maxLevel" => 52,
            "effect" => "A cada nível de expanção permite a colonização de um novo planeta"
        ]);

        DB::table('builds')->insert([
            "name" => "Cristal Mining",
            "code" => 6,
            "image" => "build-06.png",
            "description" => "Máquina de mineração de cristal, a cada nível aumenta a capacidade de minerar cristal, essa máquina exige energia como custo para manter o funcionamento. O cristal é usado para o desenvolvimento da tecnologia espacial e máquinas de guerra especiais.",
            "maxLevel" => 52,
            "effect" => "A cada nível de expanção permite a colonização de um novo planeta"
        ]);

        DB::table('builds')->insert([
            "name" => "Laboratory",
            "code" => 8,
            "image" => "build-08.png",
            "description" => "Edifício de pesquisa tecnológica, requer energia para funcionamento. Acumula pontos de pesquisa, os pontos de pesquisa são utilizados para descoberta e liberação de novas tecnologias. Cada tecnologia descoberta traz algum recurso ou vantagem especial no jogo.",
            "maxLevel" => 52,
            "effect" => "A cada nível de expanção permite a colonização de um novo planeta"
        ]);

        DB::table('builds')->insert([
            "name" => "Warehouse",
            "code" => 9,
            "image" => "build-09.png",
            "description" => "Armazena os recursos coletados, quanto maior o nível maior a capacidade de armazenamento, os recursos que não couberem no depósito ficam vulneráveis a roubo de outros jogadores",
            "maxLevel" => 52,
            "effect" => "A cada nível de expanção permite a colonização de um novo planeta"
        ]);

        DB::table('builds')->insert([
            "name" => "Spaceship Factory",
            "code" => 10,
            "image" => "build-10.png",
            "description" => "Fabrica de construções de naves",
            "maxLevel" => 52,
            "effect" => "A cada nível de expanção permite a colonização de um novo planeta"
        ]);

        DB::table('builds')->insert([
            "name" => "Batery House",
            "code" => 11,
            "image" => "build-11.png",
            "description" => "Descricao para casa de baterias.",
            "maxLevel" => 52,
            "effect" => "A cada nível de expanção permite a colonização de um novo planeta"
        ]);

        DB::table('builds')->insert([
            "name" => "Military Camp",
            "code" => 12,
            "image" => "build-12.png",
            "description" => "Descricao para military camp.",
            "maxLevel" => 52,
            "effect" => "A cada nível de expanção libera a construção de novas unidades"
        ]);

        DB::table('builds')->insert([
            "name" => "Shield",
            "code" => 13,
            "image" => "build-13.png",
            "description" => "Escudo de força, protege o planeta de ataques de outros jogadores, a cada nível aumenta a capacidade de absorção de danos.",   
            "maxLevel" => 52,
            "effect" => "A cada nível de expanção libera a construção de novas unidades"
        ]);
    }
}
