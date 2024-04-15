<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('builds')->insert([
            "name" => "Tritium Mining",
            "code" => "15",
            "image" => "build-15.png",
            "description" => "Advanced Tritium extraction facility. Uses cutting-edge technology for efficient mining and quick processing, ensuring a steady resource flow.",
            "effect"=> "Each level increases tritium mining efficiency",
            "metalStart" => 500,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 1,
            "crystalLevel" => 1,
            "coefficient" => 300
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
