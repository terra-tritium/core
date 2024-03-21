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
        Log::info("Ajuste efeitos Shipyard");

        DB::table('builds')->where('name','Shipyard')->update(
            ['effect' => "Command your starships and shape your faction's destiny in the cosmos of Terra Tritium."]
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
