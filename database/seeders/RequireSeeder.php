<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RequireSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
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
    }
}
