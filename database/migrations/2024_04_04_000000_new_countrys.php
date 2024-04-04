<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('countrys')->insert([
            'name' => "South Africa",
            'code' => "SA",
            'image' => "sa.svg",
        ]);

        DB::table('countrys')->insert([
            'name' => "Uruguay",
            'code' => "UY",
            'image' => "uy.svg",
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
