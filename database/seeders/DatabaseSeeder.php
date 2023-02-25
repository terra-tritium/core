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
            "name" => "Deuterium Mining",
            "code" => 5,
            "image" => "build-05.png",
            "description" => "Máquina de mineração de deuterium, a cada nível aumenta a capacidade de minerar deuterium, essa máquina exige energia como custo para manter o funcionamento. O deuterium é um recurso nobre usado nas construções, máquinas e naves específicas.",
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
            "description" => "A Casa de Bateria é um monumento à inovação e à sustentabilidade, onde a tecnologia avançada se une a preocupação com o meio ambiente para garantir uma fonte de energia limpa e renovável. É um santuário da eficiência energética, onde painéis solares, baterias avançadas e outras tecnologias trabalham juntos para armazenar e distribuir energia de forma eficiente e confiável. É a visão de um futuro onde a humanidade pode satisfazer suas necessidades energéticas sem prejudicar o planeta, mas sim protegê-lo para as gerações futuras.",
            "maxLevel" => 52,
            "effect" => "A cada nível, aumenta a eficiencia de armazenamento de energia"
        ]);

        DB::table('builds')->insert([
            "name" => "Military Camp",
            "code" => 12,
            "image" => "build-12.png",
            "description" => "O campo de treinamento militar é uma academia celestial de excelência, onde as mentes brilhantes e os corpos fortes se unem para formar os guerreiros espaciais. É um ambiente sofisticado e avançado, equipado com tecnologias de simulação e treinamento avançadas, desenvolvidas para preparar os guerreiros para as missões mais desafiadoras no espaço. É a visão de um futuro onde a humanidade está preparada para explorar, proteger e preservar o universo. É um testemunho da dedicação humana à segurança e à defesa da vida e da liberdade, trazendo esperança e paz para as gerações futuras.",
            "maxLevel" => 52,
            "effect" => "A cada nível, permite o desenvolvimento de novas armas e máquinas, além do aumento de capacidade de criação de novos rôbos militares."
        ]);

        DB::table('requires')->insert([
            "build" => 1,
            "level" => 1,
            "metal" => 200,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 1,
            "level" => 2,
            "metal" => 28000,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 2,
            "level" => 1,
            "metal" => 200,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 2,
            "level" => 2,
            "metal" => 1000,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 3,
            "level" => 1,
            "metal" => 200,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 3,
            "level" => 2,
            "metal" => 1000,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 4,
            "level" => 1,
            "metal" => 200,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 4,
            "level" => 2,
            "metal" => 1000,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 5,
            "level" => 1,
            "metal" => 200,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 5,
            "level" => 2,
            "metal" => 1000,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 6,
            "level" => 1,
            "metal" => 200,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 6,
            "level" => 2,
            "metal" => 1000,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 7,
            "level" => 1,
            "metal" => 200,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 7,
            "level" => 2,
            "metal" => 1000,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 8,
            "level" => 1,
            "metal" => 200,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 8,
            "level" => 2,
            "metal" => 1000,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 9,
            "level" => 1,
            "metal" => 200,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 9,
            "level" => 2,
            "metal" => 1000,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 10,
            "level" => 1,
            "metal" => 200,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 10,
            "level" => 2,
            "metal" => 1000,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 11,
            "level" => 1,
            "metal" => 200,
            "deuterium" => 0,
            "crystal" => 0,
            "time" => 2
        ]);

        DB::table('requires')->insert([
            "build" => 11,
            "level" => 2,
            "metal" => 1000,
            "deuterium" => 0,
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
            'deuterium' => 0,
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
            'deuterium' => 0,
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
            'deuterium' => 0,
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
            'deuterium' => 0,
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
            'deuterium' => 0,
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
            'deuterium' => 0,
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
            'deuterium' => 0,
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
            'deuterium' => 0,
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
            'deuterium' => 0,
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
            'deuterium' => 0,
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
            'deuterium' => 0,
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
            'deuterium' => 0,
            'crystal' => 0,
            'time' => 5
        ]);

    }
}
