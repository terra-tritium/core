<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove a foreign key (precisa ser removida antes de alterar o tipo)
        Schema::table('combats', function (Blueprint $table) {
            $table->dropForeign('combats_planet_foreign');
        });

        // Altera o tipo da coluna de BIGINT para VARCHAR
        DB::statement('ALTER TABLE combats MODIFY planet VARCHAR(255)');
    }

    public function down(): void
    {
        // Reverte o tipo de volta para BIGINT
        DB::statement('ALTER TABLE combats MODIFY planet BIGINT');

        // Recria a foreign key (ajuste "planets" se necessário)
        Schema::table('combats', function (Blueprint $table) {
            $table->foreign('planet')->references('id')->on('planets');
        });
    }
};
