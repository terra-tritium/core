<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aliances', function (Blueprint $table) {
            $table->id();
            $table->string("description");
            $table->bigInteger("energy");
            $table->bigInteger("score");
            $table->bigInteger("buildScore");
            $table->bigInteger("labScore");
            $table->bigInteger("tradeScore");
            $table->bigInteger("attackScore");
            $table->bigInteger("defenseScore");
            $table->bigInteger("warScore");
        });
        Schema::create('planets', function (Blueprint $table) {
            $table->id();
            $table->string("address");
            $table->string("name");
            $table->string('resource');
            $table->integer("level");
            $table->integer("region");
            $table->integer("quadrant");
            $table->integer("position");
            $table->integer("humanoids");
            $table->string('status');
        });
        Schema::create('countrys', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("code");
            $table->string("image");           
        });
        Schema::create('modes', function (Blueprint $table) {
            $table->id();
            $table->integer("code");
            $table->string("name");
            $table->string("image");
            $table->string("description");
        });
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("address");
            $table->timestamp("since")->useCurrent();
            $table->foreignId('country')->constrained("countrys");
            $table->integer('gameMode');
            $table->integer('aliance')->nullable()->unsigned();
            $table->bigInteger("ready")->nullable();
            $table->bigInteger("metal");
            $table->bigInteger("deuterium");
            $table->bigInteger("crystal");
            $table->bigInteger("energy");
            $table->bigInteger("battery");
            $table->bigInteger("extraBattery");
            $table->bigInteger("capMetal");
            $table->bigInteger("capDeuterium");
            $table->bigInteger("capCrystal");
            $table->bigInteger("proMetal");
            $table->bigInteger("proDeuterium");
            $table->bigInteger("proCrystal");
            $table->integer("pwMetal");
            $table->integer("pwDeuterium");
            $table->integer("pwCrystal");
            $table->integer("pwEnergy");
            $table->bigInteger("timeMetal")->nullable();
            $table->bigInteger("timeDeuterium")->nullable();
            $table->bigInteger("timeCrystal")->nullable();
            $table->bigInteger("timeEnergy")->nullable();
            $table->bigInteger("merchantShips");
            $table->bigInteger("score");
            $table->bigInteger("buildScore");
            $table->bigInteger("attackScore");
            $table->bigInteger("defenseScore");
            $table->bigInteger("militaryScore");
        });
        Schema::create('logbook', function (Blueprint $table) {
            $table->id();
            $table->string("text");
            $table->foreignId('player')->constrained("players");
            $table->foreignId('planet')->constrained("planets");
        });
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string("text");
            $table->string("from");
            $table->string("to");
            $table->boolean("read");
        });
        Schema::create('requires', function (Blueprint $table) {
            $table->id();
            $table->integer('build');
            $table->integer("level");
            $table->bigInteger("metal");
            $table->bigInteger("deuterium");
            $table->bigInteger("crystal");
            $table->bigInteger("time");
        });
        Schema::create('builds', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->integer("code");
            $table->string("image");
            $table->text("description");
            $table->integer("maxLevel");
            $table->string("effect");
        });
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('build')->constrained("builds");
            $table->foreignId('planet')->constrained("planets");
            $table->integer("level");
            $table->integer("slot");
            $table->integer("workers");
            $table->bigInteger("ready")->nullable();
        });
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("description");           
        });
        Schema::create('travels', function (Blueprint $table) {
            $table->id();
            $table->string("from");
            $table->string("to");
            $table->bigInteger("duration");
            $table->bigInteger("start");
            $table->foreignId('action')->constrained("actions");
        });
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("nick");
            $table->text("description");
            $table->string("image");
            $table->string("type");
            $table->integer("defense");
            $table->integer("attack");
            $table->integer("life");
            $table->bigInteger("metal");
            $table->bigInteger("deuterium");
            $table->bigInteger("crystal");
            $table->bigInteger("time");
        });
        Schema::create('troop', function (Blueprint $table) {
            $table->id();
            $table->string("address");
            $table->foreignId('planet')->constrained("planets");
            $table->foreignId('unit')->constrained("units");
            $table->bigInteger("quantity");
        });
        Schema::create('fleet', function (Blueprint $table) {
            $table->id();
            $table->string("address");
            $table->foreignId('planet')->constrained("planets");
            $table->foreignId('unit')->constrained("units");
            $table->bigInteger("quantity");
        });
        Schema::create('production', function (Blueprint $table) {
            $table->id();
            $table->string("address");
            $table->foreignId('planet')->constrained("planets");
            $table->json('objects');
            $table->bigInteger("ready");
            $table->boolean("executed");
        });
        Schema::create('researchs', function (Blueprint $table) {
            $table->id();
            $table->integer("code");
            $table->string("title");
            $table->text("description");
            $table->integer("cost");
            $table->string("dependence");
            $table->integer("category");
        });
        Schema::create('researcheds', function (Blueprint $table) {
            $table->id();
            $table->string("address");
            $table->integer("code");
            $table->integer("points");
            $table->integer("cost");
            $table->integer("power");
            $table->bigInteger("timer");
            $table->integer("status");
        });
        Schema::create('effects', function (Blueprint $table) {
            $table->id();
            $table->string("address");
            $table->integer("speedProduceUnit");
            $table->integer("speedProduceShip");
            $table->integer("speedBuild");
            $table->integer("speedResearch");
            $table->integer("speedTravel");
            $table->integer("speedMining");
            $table->integer("costBuild");
            $table->integer("protect");
            $table->integer("extraAttack");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
