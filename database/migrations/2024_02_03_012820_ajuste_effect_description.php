<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Log::info("Ajuste efeitos builds");

        DB::table('builds')->where('name','Shipyard')->update(
            ['effect' => 'Choose your starships wisely, customize their settings, and lead your faction to supremacy in the ever-expanding universe of Terra Tritium. The fate of your faction rests among the stars — command your fleet and forge your destiny among the cosmos!']
        );

        DB::table('builds')->where('name','Laboratory')->update(
            ['effect' => 'Enables the Scientist guardian.At each level, it allows the discovery and development of new technologies']
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
