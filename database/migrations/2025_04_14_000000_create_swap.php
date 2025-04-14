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
        Schema::create('swap', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('user')->constrained("users")->index();
            $table->string('memo');
            $table->string('txhash');
            $table->integer('tritium');
            $table->integer("used")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfts_used');
    }
};
