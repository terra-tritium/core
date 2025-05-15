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
        Schema::table('players', function (Blueprint $table) {
            $table->string("infesta")->default(1)->nullable();
            $table->string("simbion")->default(1)->nullable();
            $table->string("tantra")->default(1)->nullable();
            $table->string("xantii")->default(1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn(["infesta", "simbion", "tantra", "xantii"]);
        });
    }
};
