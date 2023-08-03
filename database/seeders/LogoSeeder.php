<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogoSeeder  extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 433; $i++) {
            $interator = "0";
            if ($i < 10)
                $interator = "00" . $i;
            else if ($i < 99)
                $interator = "0" . $i;
            else
                $interator = $i;

            $nomeLogo = "logo_fill_" . $interator . ".jpg";
        
            DB::table('logo')->insert(
                [
                    'name' => $nomeLogo,
                    'alt' => $nomeLogo,
                    'available' =>true
                ]
            );
        }
    }
}
