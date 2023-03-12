<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countrys')->insert([
            "name" => "United States",
            "code" => "USA",
            "image" => "Vazio"
        ]);

        DB::table('countrys')->insert([
            "name" => "Brazil",
            "code" => "BRA",
            "image" => "Vazio"
        ]);

        DB::table('countrys')->insert([
            "name" => "Italy",
            "code" => "ITA",
            "image" => "Vazio"
        ]);

        DB::table('countrys')->insert([
            "name" => "Germany",
            "code" => "GER",
            "image" => "Vazio"
        ]);

        DB::table('countrys')->insert([
            "name" => "France",
            "code" => "FRA",
            "image" => "Vazio"
        ]);

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
            "name" => "Slipway",
            "code" => 10,
            "image" => "build-10.png",
            "description" => "Armazena os recursos coletados, quanto maior o nível maior a capacidade de armazenamento, os recursos que não couberem no depósito ficam vulneráveis a roubo de outros jogadores",
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

        DB::table('requires')->insert([
            "build" => 1,
            "level" => 1,
            "metal" => 200,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 1,
            "level" => 2,
            "metal" => 28000,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 2,
            "level" => 1,
            "metal" => 200,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 2,
            "level" => 2,
            "metal" => 1000,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 3,
            "level" => 1,
            "metal" => 200,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 3,
            "level" => 2,
            "metal" => 1000,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 4,
            "level" => 1,
            "metal" => 200,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 4,
            "level" => 2,
            "metal" => 1000,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 5,
            "level" => 1,
            "metal" => 200,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 5,
            "level" => 2,
            "metal" => 1000,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 6,
            "level" => 1,
            "metal" => 200,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 6,
            "level" => 2,
            "metal" => 1000,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 7,
            "level" => 1,
            "metal" => 200,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 7,
            "level" => 2,
            "metal" => 1000,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 8,
            "level" => 1,
            "metal" => 200,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 8,
            "level" => 2,
            "metal" => 1000,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 9,
            "level" => 1,
            "metal" => 200,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 9,
            "level" => 2,
            "metal" => 1000,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 10,
            "level" => 1,
            "metal" => 200,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 10,
            "level" => 2,
            "metal" => 1000,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 11,
            "level" => 1,
            "metal" => 200,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 11,
            "level" => 2,
            "metal" => 1000,
            "uranium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@com.br',
            'password' => bcrypt('123456'),
        ]);

        DB::table('units')->insert([
            'name' => 'Soldier T1',
            'nick' => 'Soldier',
            'description' => 'Texto de descrição do T1',
            'image' => 'droid-01.png',
            'type' => "droid",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('units')->insert([
            'name' => 'Marine FFR02',
            'nick' => 'Marine',
            'description' => 'Texto de descrição do FFR02',
            'image' => 'droid-02.png',
            'type' => "droid",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('units')->insert([
            'name' => 'Sentinela BALT3',
            'nick' => 'Sentinela',
            'description' => 'Texto de descrição do BALT3',
            'image' => 'droid-03.png',
            'type' => "droid",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('units')->insert([
            'name' => 'Trooper Archer ARW4',
            'nick' => 'Trooper',
            'description' => 'Texto de descrição do ARW4',
            'image' => 'droid-04.png',
            'type' => "droid",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('units')->insert([
            'name' => 'Laucher CMC5',
            'nick' => 'Laucher',
            'description' => 'Texto de descrição do CMC5',
            'image' => 'droid-05.png',
            'type' => "droid",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('units')->insert([
            'name' => 'Bomber PHP6',
            'nick' => 'Bomber',
            'description' => 'Texto de descrição do PHP6',
            'image' => 'droid-06.png',
            'type' => "droid",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('units')->insert([
            'name' => 'Screw Laucher SWL123',
            'nick' => 'Screw',
            'description' => 'Texto de descrição do SWL123',
            'image' => 'droid-07.png',
            'type' => "especial",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('units')->insert([
            'name' => 'Killer Drone KD250',
            'nick' => 'Killer',
            'description' => 'Texto de descrição do KD250',
            'image' => 'droid-08.png',
            'type' => "especial",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('units')->insert([
            'name' => 'MovTermShield MTS20',
            'nick' => 'MovShield',
            'description' => 'Texto de descrição do MTS20',
            'image' => 'droid-09.png',
            'type' => "vehicle",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('units')->insert([
            'name' => 'Energy Claw ENC30',
            'nick' => 'Claw',
            'description' => 'Texto de descrição do ENC30',
            'image' => 'droid-10.png',
            'type' => "vehicle",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('units')->insert([
            'name' => 'Glower Sky GSY40',
            'nick' => 'Glower',
            'description' => 'Texto de descrição do GSY40',
            'image' => 'droid-11.png',
            'type' => "launcher",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('units')->insert([
            'name' => 'Rocket Rainer RR50',
            'nick' => 'Rocket',
            'description' => 'Texto de descrição do RR50',
            'image' => 'droid-12.png',
            'type' => "launcher",
            'defense' => 5,
            'attack' => 5,
            'life' => 100,
            'metal' => 500,
            'uranium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

        DB::table('researchs')->insert([
            'code' => 100,
            'title' => 'Economia de Energia',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "0",
            'category' => 2,
        ]);

        DB::table('researchs')->insert([
            'code' => 200,
            'title' => 'Extração de minerio nobre',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "100",
            'category' => 2,
        ]);

        DB::table('researchs')->insert([
            'code' => 300,
            'title' => 'Defesa',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "200",
            'category' => 2,
        ]);

        DB::table('researchs')->insert([
            'code' => 400,
            'title' => 'Mecanica espacial',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "300",
            'category' => 1,
        ]);

        DB::table('researchs')->insert([
            'code' => 500,
            'title' => 'Explorador',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "300",
            'category' => 3,
        ]);

        DB::table('researchs')->insert([
            'code' => 600,
            'title' => 'Robotica',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "400",
            'category' => 1,
        ]);

        DB::table('researchs')->insert([
            'code' => 700,
            'title' => 'Comercio',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "500",
            'category' => 3,
        ]);

        DB::table('researchs')->insert([
            'code' => 800,
            'title' => 'Nanotecnologia',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "600",
            'category' => 1,
        ]);

        DB::table('researchs')->insert([
            'code' => 900,
            'title' => 'Expansão',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "300,600,700",
            'category' => 2,
        ]);

        DB::table('researchs')->insert([
            'code' => 1000,
            'title' => 'Tritium',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "500",
            'category' => 3,
        ]);

        DB::table('researchs')->insert([
            'code' => 1100,
            'title' => 'Bateria',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "800",
            'category' => 1,
        ]);

        DB::table('researchs')->insert([
            'code' => 1200,
            'title' => 'Diplomacia',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "900",
            'category' => 2,
        ]);

        DB::table('researchs')->insert([
            'code' => 1300,
            'title' => 'Plasma',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "1100",
            'category' => 1,
        ]);

        DB::table('researchs')->insert([
            'code' => 1400,
            'title' => 'Centro de Armazenamento',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "1100,1200",
            'category' => 3,
        ]);

        DB::table('researchs')->insert([
            'code' => 1500,
            'title' => 'Gravidade',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "1300,1400",
            'category' => 2,
        ]);

        DB::table('researchs')->insert([
            'code' => 1600,
            'title' => 'Reflexo de Lux',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "1500",
            'category' => 3,
        ]);

        DB::table('researchs')->insert([
            'code' => 1700,
            'title' => 'Reflexo de Lux',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "1500",
            'category' => 2,
        ]);

        DB::table('researchs')->insert([
            'code' => 1800,
            'title' => 'Hipervelocidade',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "1700",
            'category' => 1,
        ]);

        DB::table('researchs')->insert([
            'code' => 1900,
            'title' => 'Fabrica',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "1700",
            'category' => 2,
        ]);

        DB::table('researchs')->insert([
            'code' => 2000,
            'title' => 'Governo',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "1900",
            'category' => 2,
        ]);

        DB::table('researchs')->insert([
            'code' => 2100,
            'title' => 'Localizador',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "1800,1900",
            'category' => 3,
        ]);

        DB::table('researchs')->insert([
            'code' => 2200,
            'title' => 'Fonte de energia',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "1900",
            'category' => 2,
        ]);

        DB::table('researchs')->insert([
            'code' => 2300,
            'title' => 'Destroyer',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "2200",
            'category' => 1,
        ]);

        DB::table('researchs')->insert([
            'code' => 2400,
            'title' => 'Black trade',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "2200",
            'category' => 2,
        ]);

        DB::table('researchs')->insert([
            'code' => 2500,
            'title' => 'Tecnologia Alien',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "2300",
            'category' => 1,
        ]);

        DB::table('researchs')->insert([
            'code' => 2600,
            'title' => 'Filtro de tritium',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "2200,2300",
            'category' => 3,
        ]);

        DB::table('researchs')->insert([
            'code' => 2700,
            'title' => 'Transcendência tecnologica 1',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "2500",
            'category' => 2,
        ]);

        DB::table('researchs')->insert([
            'code' => 2800,
            'title' => 'Transcendência militar 1',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "2500",
            'category' => 1,
        ]);

        DB::table('researchs')->insert([
            'code' => 2900,
            'title' => 'Transcendência espacial 1',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "2500",
            'category' => 3,
        ]);

        DB::table('researchs')->insert([
            'code' => 3000,
            'title' => 'Tritium power mining 1',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "2700,2800,2900",
            'category' => 2,
        ]);

        DB::table('researchs')->insert([
            'code' => 3100,
            'title' => 'Transcendência tecnologica 2',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "2700",
            'category' => 2,
        ]);

        DB::table('researchs')->insert([
            'code' => 3200,
            'title' => 'Transcendência militar 2',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "2800",
            'category' => 1,
        ]);

        DB::table('researchs')->insert([
            'code' => 3300,
            'title' => 'Transcendência espacial 2',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "2900",
            'category' => 3,
        ]);

        DB::table('researchs')->insert([
            'code' => 3400,
            'title' => 'Tritium power mining 2',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "3100,3200,3300",
            'category' => 2,
        ]);

        DB::table('researchs')->insert([
            'code' => 3500,
            'title' => 'Transcendência tecnologica 3',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "3100",
            'category' => 2,
        ]);

        DB::table('researchs')->insert([
            'code' => 3600,
            'title' => 'Transcendência militar 3',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "3200",
            'category' => 1,
        ]);
        
        DB::table('researchs')->insert([
            'code' => 3700,
            'title' => 'Transcendência espacial 3',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "3300",
            'category' => 3,
        ]);

        DB::table('researchs')->insert([
            'code' => 3800,
            'title' => 'Tritium power mining 3',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => "3500,3600,3700",
            'category' => 2,
        ]);

        DB::table('modes')->insert([
            'name' => "Conquer",
            'code' => 1,
            'description' => 'description',
            'image' => 'mode01.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Space Titan",
            'code' => 2,
            'description' => 'description',
            'image' => 'mode01.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Researcher",
            'code' => 3,
            'description' => 'description',
            'image' => 'mode01.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Engineer",
            'code' => 4,
            'description' => 'description',
            'image' => 'mode01.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Protector",
            'code' => 5,
            'description' => 'description',
            'image' => 'mode01.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Builder",
            'code' => 6,
            'description' => 'description',
            'image' => 'mode01.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Navigator",
            'code' => 7,
            'description' => 'description',
            'image' => 'mode01.png'
        ]);

        DB::table('modes')->insert([
            'name' => "Miner",
            'code' => 8,
            'description' => 'description',
            'image' => 'mode01.png'
        ]);

        DB::table('strategys')->insert([
            'name' => "Wedge",
            'code' => 1,
            'description' => 'description',
            'image' => 'strategy01.png'
        ]);

        DB::table('strategys')->insert([
            'name' => "Delta",
            'code' => 2,
            'description' => 'description',
            'image' => 'strategy02.png'
        ]);

        DB::table('strategys')->insert([
            'name' => "Line",
            'code' => 3,
            'description' => 'description',
            'image' => 'strategy03.png'
        ]);

        DB::table('strategys')->insert([
            'name' => "Sniper",
            'code' => 4,
            'description' => 'description',
            'image' => 'strategy04.png'
        ]);

        DB::table('strategys')->insert([
            'name' => "Column",
            'code' => 5,
            'description' => 'description',
            'image' => 'strategy05.png'
        ]);

        DB::table('strategys')->insert([
            'name' => "Diamond",
            'code' => 6,
            'description' => 'description',
            'image' => 'strategy06.png'
        ]);

        DB::table('strategys')->insert([
            'name' => "Star",
            'code' => 7,
            'description' => 'description',
            'image' => 'strategy07.png'
        ]);

        DB::table('strategys')->insert([
            'name' => "Diagonal",
            'code' => 8,
            'description' => 'description',
            'image' => 'strategy08.png'
        ]);

        DB::table('strategys')->insert([
            'name' => "Dual Column",
            'code' => 9,
            'description' => 'description',
            'image' => 'strategy09.png'
        ]);

        DB::table('strategys')->insert([
            'name' => "Flanks",
            'code' => 10,
            'description' => 'description',
            'image' => 'strategy10.png'
        ]);
    }
}
