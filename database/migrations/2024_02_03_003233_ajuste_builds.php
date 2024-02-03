<?php

use App\Models\Building;
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
        Log::info('Iniciando a execução da migração correção de Shipyard.');
        $result = DB::select("
                        SELECT id
                        FROM buildings
                        WHERE build = 1
                        AND planet IN (
                            SELECT planet
                            FROM buildings
                            WHERE build IN (1, 11)
                            GROUP BY planet
                            HAVING COUNT(DISTINCT build) >= 2
                        )");

        if (!empty($result)) {
            foreach ($result as $r) {
                DB::table('buildings')->where('id', '=', $r->id)->delete();
                Log::info("removeu Shipyard".$r->id);
            }
        } else {
            Log::info("Nenhum registro encontrado para a Shipyard");
        }
        //para os planetas que não possuem 2 construções Shipyard, mas construiu o Shipyard de id 1
        // Shipyard de id 1 sera deletado e manterá apenas o com id 11
        DB::table('buildings')->where('build', '=', 1)->update(['build' => 11]);

        DB::table('builds')->where('id', '=', 1)->delete();
        Log::info('Finalizando a correção de Shipyard.');


        Log::info('Iniciando a execução da migração correção de market.');
        $result = DB::select("
                        SELECT id
                        FROM buildings
                        WHERE build = 2
                        AND planet IN (
                            SELECT planet
                            FROM buildings
                            WHERE build IN (2, 15)
                            GROUP BY planet
                            HAVING COUNT(DISTINCT build) >= 2
                        )");

        if (!empty($result)) {
            foreach ($result as $r) {
                DB::table('buildings')->where('id', '=', $r->id)->delete();
                Log::info("removeu mercado".$r->id);
            }
        } else {
            Log::info("Nenhum registro encontrado para a market");
        }
        //para os planetas que não possuem 2 construções Shipyard, mas construiu o Shipyard de id 1
        // Shipyard de id 1 sera deletado e manterá apenas o com id 11
        DB::table('buildings')->where('build', '=', 2)->update(['build' => 15]);

        DB::table('builds')->where('id', '=', 2)->delete();
        Log::info('fim da correção de market.');


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
