<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('builds')->insert([
            "name" => "Shipyard",
            "code" => 9,
            "image" => "build-10.png",
            "description" => "Shipyard construction factory",
            "effect" => "",
            "metalStart" => 100,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 10,
            "crystalLevel" => 20,
            "coefficient" => 50
        ]);

        DB::table('builds')->insert([
            "name" => "Market",
            "code" => 13,
            "image" => "build-18.png",
            "description" => "Resource trades between players",   
            "effect" => "With each level of expansion, the fee rate is reduced and the trading range is increased",
            "metalStart" => 100,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 10,
            "crystalLevel" => 20,
            "coefficient" => 50
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
