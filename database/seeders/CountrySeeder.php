<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
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
            "image" => "us.svg"
        ]);

        DB::table('countrys')->insert([
            "name" => "Brazil",
            "code" => "BRA",
            "image" => "br.svg"
        ]);

        DB::table('countrys')->insert([
            "name" => "Italy",
            "code" => "ITA",
            "image" => "it.svg"
        ]);

        DB::table('countrys')->insert([
            "name" => "Germany",
            "code" => "GER",
            "image" => "de.svg"
        ]);

        DB::table('countrys')->insert([
            "name" => "France",
            "code" => "FRA",
            "image" => "fr.svg"
        ]);

        DB::table('countrys')->insert([
            "name" => "Czech Republic",
            "code" => "CZE",
            "image" => "cz.svg"
        ]);

        DB::table('countrys')->insert([
            "name" => "Spain",
            "code" => "ESP",
            "image" => "es.svg"
        ]);

        DB::table('countrys')->insert([
            "name" => "Poland",
            "code" => "POL",
            "image" => "pl.svg"
        ]);

        DB::table('countrys')->insert([
            "name" => "Russia",
            "code" => "RUS",
            "image" => "ru.svg"
        ]);

        DB::table('countrys')->insert([
            "name" => "China",
            "code" => "CHN",
            "image" => "cn.svg"
        ]);

        DB::table('countrys')->insert([
            "name" => "Argentina",
            "code" => "ARG",
            "image" => "ar.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "United Arab Emirates",
            "code" => "ARE",
            "image" => "ae.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "Bulgaria",
            "code" => "BGR",
            "image" => "bg.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "Denmark",
            "code" => "DNK",
            "image" => "dk.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "United Kingdom",
            "code" => "GBR",
            "image" => "gb.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "Greece",
            "code" => "GRC",
            "image" => "gr.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "Hungary",
            "code" => "HUN",
            "image" => "hu.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "Israel",
            "code" => "ISR",
            "image" => "il.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "Lithuania",
            "code" => "LTU",
            "image" => "lt.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "Latvia",
            "code" => "LVA",
            "image" => "lv.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "Mexico",
            "code" => "MEX",
            "image" => "mx.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "Portugal",
            "code" => "PRT",
            "image" => "pt.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "Romania",
            "code" => "ROU",
            "image" => "ro.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "Serbia",
            "code" => "SRB",
            "image" => "rs.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "Sweden",
            "code" => "SWE",
            "image" => "se.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "Slovenia",
            "code" => "SVN",
            "image" => "si.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "Slovakia",
            "code" => "SVK",
            "image" => "sk.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "Turkey",
            "code" => "TUR",
            "image" => "tr.svg"
        ]);
        
        DB::table('countrys')->insert([
            "name" => "Thailand",
            "code" => "THA",
            "image" => "th.svg"
        ]);

        DB::table('countrys')->insert([
            "name" => "Japan",
            "code" => "JPN",
            "image" => "jp.svg"
        ]);

        DB::table('countrys')->insert([
            "name" => "South Korea",
            "code" => "KOR",
            "image" => "kr.svg"
        ]);
        
        
        
    }
}