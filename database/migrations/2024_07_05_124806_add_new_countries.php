<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('paises')->insert([
            'nome' => "Australia",
            'codigo' => "AU",
            'imagem' => "au.svg",
        ]);
        
        DB::table('paises')->insert([
            'nome' => "New Zealand",
            'codigo' => "NZ",
            'imagem' => "nz.svg",
        ]);
        
        DB::table('paises')->insert([
            'nome' => "Norway",
            'codigo' => "NO",
            'imagem' => "no.svg",
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
