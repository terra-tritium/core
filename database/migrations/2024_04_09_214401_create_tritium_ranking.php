<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challanges', function (Blueprint $table) {
            $table->id()->index();
            $table->integer("first")->default(0);
            $table->integer("second")->default(0);
            $table->integer("third")->default(0);
            $table->integer("fourth")->default(0);
            $table->integer("fifth")->default(0);
            $table->integer("firstScore")->default(0);
            $table->integer("secondScore")->default(0);
            $table->integer("thirdScore")->default(0);
            $table->integer("fourthScore")->default(0);
            $table->integer("fifthScore")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
