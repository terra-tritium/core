<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $letter = 'A';
        for ($i = 1; $i <= 16; $i++) {
            $quadrant = $letter . '000';
            $name = 'market ' . $quadrant;
            DB::table('market')->insert(['region'=>$letter,'quadrant' => $quadrant, 'name' => $name]);        
            $letter++;
        }
    }
}

