<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    
        
        DB::table('unitsShipyard')->where('id', '1')->update([
            'image' => 'ship-01.png',
        ]);

        DB::table('unitsShipyard')->where('id', '2')->update([
            'image' => 'ship-02.png',
        ]);

        DB::table('unitsShipyard')->where('id', '3')->update([
            'image' => 'ship-03.png',
        ]);

        DB::table('unitsShipyard')->where('id', '4')->update([
            'image' => 'ship-04.png',
        ]);

        DB::table('unitsShipyard')->where('id', '5')->update([
            'image' => 'ship-05.png',
        ]);

        DB::table('unitsShipyard')->where('id', '6')->update([
            'image' => 'ship-06.png',
        ]);

        DB::table('unitsShipyard')->where('id', '7')->update([
            'image' => 'ship-07.png',
        ]);

        DB::table('unitsShipyard')->where('id', '8')->update([
            'image' => 'ship-08.png',
        ]);

        DB::table('unitsShipyard')->where('id', '9')->update([
            'image' => 'ship-09.png',
        ]);

        DB::table('unitsShipyard')->where('id', '10')->update([
            'image' => 'ship-10.png',
        ]);

        DB::table('unitsShipyard')->where('id', '11')->update([
            'image' => 'ship-11.png',
        ]);

        DB::table('unitsShipyard')->where('id', '12')->update([
            'image' => 'ship-12.png',
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
