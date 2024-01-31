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
        DB::table('nft-effects')->insert([
            'type' => 1,
            'rarity' => 1,
            'effect' => 'Enable the Colonizer Game Mode',            
            'value' => 0,
        ]);

        DB::table('nft-effects')->insert([
            'type' => 1,
            'rarity' => 2,
            'effect' => 'Enable the Colonizer Game Mode + 5% speed travel in Walker 07 Scout',            
            'value' => 1.0,
        ]);

        DB::table('nft-effects')->insert([
            'type' => 1,
            'rarity' => 3,
            'effect' => 'Enable the Colonizer Game Mode + 8% speed travel in Walker 07 Scout',            
            'value' => 1.0,
        ]);

        DB::table('nft-effects')->insert([
            'type' => 2,
            'rarity' => 4,
            'effect' => 'Founder Origins Asset',            
            'value' => 1.0,
        ]);

        DB::table('nft-effects')->insert([
            'type' => 2,
            'rarity' => 5,
            'effect' => 'Exclusive Founder Origins Asset',            
            'value' => 1.0,
        ]);

        DB::table('nft-effects')->insert([
            'type' => 3,
            'rarity' => 5,
            'effect' => 'Key - Combine different Key to unlock exclusive features',            
            'value' => 1.0,
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
