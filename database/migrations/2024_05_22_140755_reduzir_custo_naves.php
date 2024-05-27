<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('ships')->where('name', 'Combat Cruiser')->update(
            ['metal' => 2500]
        );

        DB::table('ships')->where('name', 'Scout Walker 7')->update(
            ['metal' => 5000]
        );

        DB::table('ships')->where('name', 'Stealth Ship')->update(
            ['metal' => 5000, 'uranium' => 1250, 'crystal' => 3500]
        );

        DB::table('ships')->where('name', 'Flagship')->update(
            ['metal' => 32500, 'uranium' => 17500, 'crystal' => 5000]
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
