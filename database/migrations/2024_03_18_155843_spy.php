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
        Schema::create('espionage', function (Blueprint $table) {
            $table->id();
            $table->integer('spy')->constrained("players");
            $table->foreignId('planet')->constrained("planets");
            $table->integer("typeSpy");
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('end_date')->useCurrent();
            $table->boolean('finished')->default(false);
            $table->boolean('success');

            $table->json('troop')->nullable();
            $table->json('fleet')->nullable(false);

            $table->integer("military")->nullable();
            $table->integer("economy")->nullable();
            $table->integer("science")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
