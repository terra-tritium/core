<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResearchSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        DB::table('researchs')->insert([
            'code' => 100,
            'title' => 'Energy Saving',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 0,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 200,
            'title' => 'Extração de minerio nobre',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 100,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 300,
            'title' => 'Defesa',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 200,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 400,
            'title' => 'Mecanica espacial',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 0,
            'category' => 1,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 500,
            'title' => 'Explorador',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 0,
            'category' => 3,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 600,
            'title' => 'Robotica',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 400,
            'category' => 1,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 700,
            'title' => 'Comercio',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 500,
            'category' => 3,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 800,
            'title' => 'Nanotecnologia',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 600,
            'category' => 1,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 900,
            'title' => 'Expansão',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 300,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1000,
            'title' => 'Tritium',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 500,
            'category' => 3,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1100,
            'title' => 'Bateria',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 800,
            'category' => 1,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1200,
            'title' => 'Diplomacia',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 900,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1300,
            'title' => 'Plasma',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 1100,
            'category' => 1,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1400,
            'title' => 'Centro de Armazenamento',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 1100,
            'category' => 3,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1500,
            'title' => 'Gravidade',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 1300,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1600,
            'title' => 'Reflexo de Lux',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 1400,
            'category' => 3,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1700,
            'title' => 'Reflexo de Lux',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 1500,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1800,
            'title' => 'Hipervelocidade',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 1300,
            'category' => 1,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1900,
            'title' => 'Fabrica',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 1700,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2000,
            'title' => 'Governo',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 1900,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2100,
            'title' => 'Localizador',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 1600,
            'category' => 3,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2200,
            'title' => 'Fonte de energia',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 2000,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2300,
            'title' => 'Destroyer',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 1800,
            'category' => 1,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2400,
            'title' => 'Black trade',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 2200,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2500,
            'title' => 'Tecnologia Alien',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 2300,
            'category' => 1,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2600,
            'title' => 'Filtro de tritium',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 2100,
            'category' => 3,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2700,
            'title' => 'Avanço tecnologica 1',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 2400,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2800,
            'title' => 'Avanço militar 1',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 2500,
            'category' => 1,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2900,  
            'title' => 'Avanço espacial 1',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 2600,
            'category' => 3,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 3000,
            'title' => 'Tritium power mining 1',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 2700,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 3100,
            'title' => 'Avanço tecnologica 2',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 3000,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 3200,
            'title' => 'Avanço militar 2',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 2800,
            'category' => 1,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 3300,
            'title' => 'Avanço espacial 2',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 2900,
            'category' => 3,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 3400,
            'title' => 'Tritium power mining 2',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 3100,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 3500,
            'title' => 'Avanço tecnologica 3',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 3400,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 3600,
            'title' => 'Avanço militar 3',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 3200,
            'category' => 1,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);
        
        DB::table('researchs')->insert([
            'code' => 3700,
            'title' => 'Avanço espacial 3',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 3300,
            'category' => 3,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 3800,
            'title' => 'Tritium power mining 3',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 3500,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);
    }
}
