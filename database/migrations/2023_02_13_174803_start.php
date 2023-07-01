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
            $table->id()->index();
            $table->string("description")->nullable();
            $table->string("name");
            $table->string('avatar')->nullable();
            $table->bigInteger("energy")->default(0);
            $table->bigInteger("score")->default(0);
            $table->bigInteger("buildScore")->default(0);
            $table->bigInteger("labScore")->default(0);
            $table->bigInteger("tradeScore")->default(0);
            $table->bigInteger("attackScore")->default(0);
            $table->bigInteger("defenseScore")->default(0);
            $table->bigInteger("warScore")->default(0);
        });

        Schema::create('aliances_ranking', function (Blueprint $table) {
            $table->id();
            $table->integer('aliance')->constrained("aliances");
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
            $table->id()->index();
            $table->integer('player')->constrained("players")->index();
            $table->string("name");
            $table->string('resource');
            $table->integer("level");
            $table->string('status');
            $table->integer('type');
            # Position
            $table->string("region");
            $table->string("quadrant");
            $table->integer("position");
            # Workers
            $table->integer("workers");
            $table->integer("workersWaiting");
            $table->integer("workersOnMetal");
            $table->integer("workersOnUranium");
            $table->integer("workersOnCrystal");
            $table->integer("workersOnLaboratory");
            # Resources
            $table->bigInteger("metal");
            $table->bigInteger("uranium");
            $table->bigInteger("crystal");
            $table->bigInteger("energy");
            $table->bigInteger("battery");
            $table->bigInteger("extraBattery");
            $table->bigInteger("capMetal");
            $table->bigInteger("capUranium");
            $table->bigInteger("capCrystal");
            $table->bigInteger("proMetal");
            $table->bigInteger("proUranium");
            $table->bigInteger("proCrystal");
            $table->bigInteger("ready")->nullable();
            # Laboratory
            $table->bigInteger("researchPoints");
            # Power multiplier of resources
            $table->integer("pwMetal");
            $table->integer("pwUranium");
            $table->integer("pwCrystal");
            $table->integer("pwEnergy");
            $table->integer("pwWorker");
            $table->integer("pwResearch");
            # Using resources
            $table->integer("useEnergyByFactory");
            # Consumes times start
            $table->integer("timeEnergyByFactory")->nullable();
            # Accumulation time start resources
            $table->bigInteger("timeMetal")->nullable();
            $table->bigInteger("timeUranium")->nullable();
            $table->bigInteger("timeCrystal")->nullable();
            $table->bigInteger("timeEnergy")->nullable();
            $table->bigInteger("timeWorker")->nullable();
            $table->bigInteger("timeResearch")->nullable();
            # Spaceships Transport
            $table->bigInteger("transportShips");
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
        Schema::create('strategies', function (Blueprint $table) {
            $table->id();
            $table->integer("code");
            $table->string("name");
            $table->string("image");
            $table->string("description");
        });
        Schema::create('players', function (Blueprint $table) {
            $table->id()->index();
            $table->string("name");
            $table->integer('user')->constrained("users")->index();
            $table->string("address")->nullable();
            $table->timestamp("since")->useCurrent();
            $table->foreignId('country')->constrained("countrys");
            $table->integer('gameMode');
            $table->integer('attackStrategy');
            $table->integer('defenseStrategy');
            $table->integer('aliance')->nullable()->unsigned();
            $table->bigInteger("ready")->nullable();
            $table->bigInteger("researchPoints");
            # Score rankings
            $table->bigInteger("score");
            $table->bigInteger("buildScore");
            $table->bigInteger("attackScore");
            $table->bigInteger("defenseScore");
            $table->bigInteger("militaryScore");
            $table->bigInteger("researchScore");
        });
        Schema::create('ranking', function (Blueprint $table) {
            $table->id();
            $table->integer("position");
            $table->string("name");
            $table->integer('player')->constrained("players");
            $table->integer('aliance')->nullable()->unsigned();
            $table->bigInteger("energy");
            $table->bigInteger("score");
            $table->bigInteger("buildScore");
            $table->bigInteger("attackScore");
            $table->bigInteger("defenseScore");
            $table->bigInteger("militaryScore");
            $table->bigInteger("researchScore");
        });
        Schema::create('logbook', function (Blueprint $table) {
            $table->id();
            $table->string("text");
            $table->foreignId('player')->constrained("players");
            $table->foreignId('planet')->constrained("planets");
        });
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('senderId');
            $table->unsignedBigInteger('recipientId');
            $table->string('content',1000);
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('readAt')->nullable();
            $table->boolean('status');
            $table->boolean("read");
            $table->foreign('senderId')->references('id')->on('users');
            $table->foreign('recipientId')->references('id')->on('users');
        });
        Schema::create('builds', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->integer("code");
            $table->string("image");
            $table->text("description");
            $table->string("effect");
            // initial cost
            $table->integer("metalStart");
            $table->integer("uraniumStart");
            $table->integer("crystalStart");
            // initial level
            $table->integer("metalLevel");
            $table->integer("uraniumLevel");
            $table->integer("crystalLevel");
            // coefficient of growth 0% - 100%
            $table->integer("coefficient");
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
        Schema::create('travels', function (Blueprint $table) {
            $table->id();
            $table->integer('player')->constrained("players");
            $table->integer("receptor");
            $table->string("from");
            $table->string("to");
            $table->bigInteger("arrival");
            $table->bigInteger("start");
            $table->integer('status');
            $table->json("troop");
            $table->json("fleet");
            $table->integer("metal");
            $table->integer("crystal");
            $table->integer("uranium");
            $table->integer("transportShips");
            // 1 - Attack
            // 2 - Defense
            // 3 - Transport
            // 4 - Colonize
            // 5 - Explore
            $table->integer("action");
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
            $table->bigInteger("uranium");
            $table->bigInteger("crystal");
            $table->bigInteger("time");
        });
        Schema::create('troop', function (Blueprint $table) {
            $table->id();
            $table->integer('player')->constrained("players");
            $table->foreignId('planet')->constrained("planets");
            $table->foreignId('unit')->constrained("units");
            $table->bigInteger("quantity");
        });
        Schema::create('fleet', function (Blueprint $table) {
            $table->id();
            $table->integer('player')->constrained("players");
            $table->foreignId('planet')->constrained("planets");
            $table->foreignId('unit')->constrained("units");
            $table->bigInteger("quantity");
        });
        Schema::create('production', function (Blueprint $table) {
            $table->id();
            $table->integer('player')->constrained("players");
            $table->foreignId('planet')->constrained("planets");
            $table->json('objects');
            $table->bigInteger("ready");
            $table->boolean("executed");
        });
        Schema::create('effects', function (Blueprint $table) {
            $table->id();
            $table->integer('player')->constrained("players");
            $table->integer("speedProduceUnit");
            $table->integer("speedProduceShip");
            $table->integer("speedBuild");
            $table->integer("speedResearch");
            $table->integer("speedTravel");
            $table->integer("speedMining");
            $table->integer("protect");
            $table->integer("extraAttack");
            $table->integer("discountEnergy");
            $table->integer("discountHumanoid");
            $table->integer("discountBuild");
        });
        Schema::create('researchs', function (Blueprint $table) {
            $table->id();
            $table->integer("code");
            $table->string("title");
            $table->text("description");
            $table->integer("cost");
            $table->integer("dependence");
            $table->integer("category");
            $table->string("effectDescription");
            $table->json('effects');
        });
        Schema::create('researcheds', function (Blueprint $table) {
            $table->id();
            $table->integer('player')->constrained("players");
            $table->integer("code");
        });
        Schema::create('battles', function (Blueprint $table) {
            $table->id();
            $table->foreignId("attack")->constrained("players");
            $table->foreignId("defense")->constrained("players");
            $table->foreignId('planet')->constrained("planets");
            $table->string("attackDemage")->nullable();
            $table->string("defenseDemage")->nullable();
            $table->integer("attackStrategy");
            $table->integer("defenseStrategy");
            $table->integer("result")->nullable();
            $table->bigInteger("start");
            $table->integer("stage");
            $table->json('resources')->nullable();
            $table->json('attackUnits');
            $table->json('defenseUnits');
            $table->json('attackReserve');
            $table->json('defenseReserve');
            $table->json('attackSlots')->nullable();
            $table->json('defenseSlots')->nullable();
        });
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->integer("number");
            $table->foreignId('battle')->constrained("battles");
            $table->string("attackDemage");
            $table->string("defenseDemage");
            $table->integer("attackStrategy");
            $table->integer("defenseStrategy");
            $table->json('attackUnits');
            $table->json('defenseUnits');
            $table->json('attackKills');
            $table->json('defenseKills');
            $table->json('attackSlots');
            $table->json('defenseSlots');
            $table->json('attackReserve');
            $table->json('defenseReserve');
            $table->boolean('attackGaveUp');
            $table->boolean('defenseGaveUp');
        });
        Schema::create('qnames', function (Blueprint $table) {
            $table->id();
            $table->string("quadrant");
            $table->string("name");
            $table->string("x");
            $table->string("y");
        });

        /**
         *INICIO DAS TABELAS REFERENTE AO MERCADO
         */
        Schema::create('market', function (Blueprint $table) {
            $table->id();
            $table->char("region",1);
            // $table->string("quadrant");
            $table->boolean('status')->default(true);
            $table->string("name");
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable();
        });
        // Schema::create('resource', function (Blueprint $table) {
        //     $table->id();
        //     $table->string("nameResource");
        //     $table->boolean('status')->default(true);
        //     $table->timestamp('createdAt')->useCurrent();
        //     $table->timestamp('updatedAt')->nullable();
        // });
        Schema::create('trading', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idPlanetCreator');
            // $table->unsignedBigInteger('idResource');
            $table->unsignedBigInteger('idMarket');
            $table->string('resource',20);
            $table->char('type',1);//
            $table->unsignedBigInteger('quantity');//
            $table->double('price',8,3);//
            $table->double('total',10,3);//
            $table->boolean('status')->default(true);
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable();
            

            $table->foreign('idPlanetCreator')->references('id')->on('planets');
            // $table->foreign('idResource')->references('id')->on('Resource');
            $table->foreign('idMarket')->references('id')->on('market');

            $table->unsignedInteger('quantity')->default(0)->change();

        });
        Schema::create('trading_finished', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idPlanetSale')->nullable();
            $table->unsignedBigInteger('idPlanetPurch')->nullable();
            $table->unsignedBigInteger('quantity');//
            $table->double('price',8,3);//
            $table->double('distance',11,3);//
            $table->unsignedBigInteger('deliveryTime');//
            $table->unsignedBigInteger('idTrading');
            $table->boolean('status')->default(true);
            $table->timestamp('finishedAt')->useCurrent();

            $table->foreign('idPlanetSale')->references('id')->on('planets');
            $table->foreign('idPlanetPurch')->references('id')->on('planets');
            $table->foreign('idTrading')->references('id')->on('trading');

            $table->unsignedInteger('quantity')->default(0)->change();
            $table->unsignedInteger('deliveryTime')->default(0)->change();

        });

        Schema::create('safe', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idPlanetCreator')->nullable();

            $table->unsignedBigInteger('idPlanetSale')->nullable();
            $table->unsignedBigInteger('idPlanetPurch')->nullable();
            $table->unsignedBigInteger('idTrading')->nullable();;
            $table->unsignedBigInteger('idMarket')->nullable();
            $table->unsignedBigInteger('quantity');//

            $table->double('price',8,3);//
            $table->double('total',8,3);//
            $table->double('distance',11,3);//
            $table->unsignedBigInteger('deliveryTime');//
           
            $table->boolean('status')->default(true);
            $table->char('type',1);//
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable();
            $table->string('resource',20);
            $table->string('currency',20);

            $table->unsignedInteger('transportShips')->default(0);

            $table->foreign('idPlanetSale')->references('id')->on('planets');
            $table->foreign('idPlanetPurch')->references('id')->on('planets');
            $table->foreign('idTrading')->references('id')->on('trading');
            $table->foreign('idMarket')->references('id')->on('market');

        });

        /**
         *FIM DAS TABELAS REFERENTE AO MERCADO
         */
        Schema::create('nftconfig', function (Blueprint $table) {
            $table->id();
            $table->integer('player')->constrained("players");
            # Slots of control panel
            $table->integer("slot1");
            $table->integer("slot2");
            $table->integer("slot3");
            $table->integer("slot4");
            # Slot commandant colonizer
            $table->integer("slot5");
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
