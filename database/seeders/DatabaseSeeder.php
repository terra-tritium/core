<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            BuildSeeder::class,
            CountrySeeder::class,
            ModeSeeder::class,
            QuadrantSeeder::class,
            ResearchSeeder::class,
            StrategySeeder::class,
            UnitSeeder::class,
            MessagesSeeder::class,
            PlayerSeeder::class
        ]);
    }
}
