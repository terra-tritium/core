<?php

namespace Database\Seeders;

use App\Models\TerrainType;
use Illuminate\Database\Seeder;

class TerrainTypeSeeder extends Seeder
{
    public function run()
    {
        $terrains = [
            'Desert' => ['energy' => 1.0, 'defenseScore' => 1.0],
            'Grass' => ['energy' => 1.4, 'defenseScore' => 1.0],
            'Ice' => ['energy' => 1.2, 'defenseScore' => 1.2], 
            'Vulcan' => ['energy' => 0.8, 'defenseScore' => 1.0],
        ];

        foreach ($terrains as $terrain => $values) {
            TerrainType::updateOrCreate(
                ['terrainType' => $terrain],
                $values
            );
        }
    }
}
