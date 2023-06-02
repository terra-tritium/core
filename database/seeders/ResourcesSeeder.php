<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResourcesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('resource')->insert([
            'id' => 1,
            'nameResource' => 'Crystal',
          
        ]);
        DB::table('resource')->insert([
            'id' => 2,
            'nameResource' => 'Metal',
        ]);
        DB::table('resource')->insert([
            'id' => 3,
            'nameResource' => 'Uranium',
        ]);
    }
}
