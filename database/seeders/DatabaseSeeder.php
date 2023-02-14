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
    }
}
