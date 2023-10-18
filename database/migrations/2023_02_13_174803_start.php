<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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
            $table->string("name")->unique();
            $table->string('logo')->nullable();
            $table->bigInteger("energy")->default(0);
            $table->bigInteger("score")->default(0);
            $table->bigInteger("buildScore")->default(0);
            $table->bigInteger("labScore")->default(0);
            $table->bigInteger("tradeScore")->default(0);
            $table->bigInteger("attackScore")->default(0);
            $table->bigInteger("defenseScore")->default(0);
            $table->bigInteger("warScore")->default(0);
            $table->integer('founder')->constrained("players");
            $table->char('status', 1)->default('F');

            // $table->bigInteger("type")->default(0)->comment('0 - Aberta , 1 - Fechada')->change();
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

        Schema::create('rank_member', function (Blueprint $table) {
            $table->id();
            $table->integer('level');
            $table->string('rankName', '100');
            $table->integer('limit')->nullable();
            $table->string('description');
            $table->boolean('visible')->default(true);
        });

        Schema::create('aliances_members', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('player_id')->constrained("players");
            $table->unsignedBigInteger('idAliance');
            $table->char('status', 1)->default('A');
            $table->string('role', 20)->default('member');
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('dateAdmission')->nullable();
            $table->timestamp('dateOf')->nullable();
            $table->unsignedBigInteger('idRank')->nullable();

            $table->foreign('idAliance')->references('id')->on('aliances');
            $table->foreign('idRank')->references('id')->on('rank_member');
        });

        Schema::create('aliances_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('player_id')->constrained("players");
            $table->bigInteger('founder_id')->constrained("players");
            ;
            $table->text('message');
            $table->boolean('status')->default(0);
            $table->timestamps();
        });

        Schema::create('logo', function (Blueprint $table) {
            $table->id();
            $table->string('name', '100')->unique();
            $table->string('alt', 100)->nullable();
            $table->boolean('available')->default(true);
        });

        Schema::create('chat_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idAliance');
            $table->string('groupName', '255')->nullabe();
            $table->timestamp('createdAt')->useCurrent();
            $table->boolean('status')->default(0);
            $table->foreign('idAliance')->references('id')->on('aliances');

        });
        /**
         * Chat entre duas alianças
         */
        Schema::create('chat_aliance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idOrigem');
            $table->unsignedBigInteger('idDestino');
            $table->timestamp('createdAt')->useCurrent();
            $table->char('status', '1')->default('A');
            $table->string('message', '255')->nullabe();
            $table->unsignedBigInteger('player');
            $table->foreign('idOrigem')->references('id')->on('aliances');
            $table->foreign('idDestino')->references('id')->on('aliances');

        });

        Schema::create('message_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idChatGroup');
            $table->unsignedBigInteger('remetenteId');
            $table->string('message', '255')->nullabe();
            $table->timestamp('createdAt')->useCurrent();
            $table->boolean('status')->default(0);

            $table->foreign('idChatGroup')->references('id')->on('chat_group');
            // $table->foreign('remetenteId')->references('id')->on('players');

        });



        //fim aliança

        //Terrain Type
        Schema::create('terrain_types', function (Blueprint $table) {
            $table->id();
            $table->string('terrainType')->unique();
            $table->float('energy');
            $table->float('defenseScore');
        });
        //Fim Terrain Type


        Schema::create('planets', function (Blueprint $table) {
            $table->id()->index();
            $table->integer('player')->constrained("players")->index();
            $table->string("name");
            $table->string('resource');
            $table->integer("level");
            $table->string('status');
            $table->integer('type');
            $table->string('terrainType');
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
            $table->dateTime('leave_aliance_date')->nullable();
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
            $table->string("type");
            $table->timestamp("date")->useCurrent();
            $table->foreignId('player')->constrained("players");
        });
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('senderId');
            $table->unsignedBigInteger('recipientId');
            $table->string('content', 1000);
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
            $table->string("from")->constrained("planets");
            $table->string("to")->constrained("planets");
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
            $table->foreignId('planet')->constrained("planets");
            $table->integer("status");
            $table->string("attackDemage")->nullable();
            $table->string("defenseDemage")->nullable();
            $table->json('attackUnits')->nullable();
            $table->json('defenseUnits')->nullable();
            $table->integer("result")->nullable();
            $table->bigInteger("start");
            $table->integer("stage");
            $table->json('resources')->nullable();
        });
        Schema::create('fighters', function (Blueprint $table) {
            $table->id();
            $table->integer('side');
            $table->integer('strategy');
            $table->integer('player')->constrained("players");
            $table->integer("battle")->constrained("battles");
            $table->foreignId('planet')->constrained("planets");
            $table->string("demage")->nullable();
            $table->bigInteger("start");
            $table->integer('stage');
            $table->json('units');
            $table->json('reserve')->nullable();
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
            $table->char("region", 1);
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
            $table->unsignedBigInteger('idPlanetInterested')->nullable();
            ;
            // $table->unsignedBigInteger('idResource');
            $table->unsignedBigInteger('idMarket');
            $table->string('resource', 20);
            $table->string('currency', 20)->default('energy');
            $table->char('type', 1); //
            $table->unsignedBigInteger('quantity'); //
            $table->double('price', 8, 3); //
            $table->double('total', 10, 3); //
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
            $table->unsignedBigInteger('idPlanetCreator')->nullable();
            $table->unsignedBigInteger('idPlanetInterested')->nullable();
            $table->unsignedBigInteger('quantity'); //
            $table->double('price', 8, 3); //
            $table->double('distance', 11, 3); //
            $table->unsignedBigInteger('deliveryTime'); //
            $table->unsignedBigInteger('idTrading')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamp('createdAt'); //
            $table->timestamp('finishedAt')->useCurrent(); //
            $table->string('resource', 20);
            $table->string('currency', 20);
            $table->unsignedBigInteger('idMarket')->nullable();
            $table->unsignedInteger('transportShips');
            $table->char('type', 1); //

            $table->foreign('idPlanetCreator')->references('id')->on('planets');
            $table->foreign('idPlanetInterested')->references('id')->on('planets');
            $table->foreign('idTrading')->references('id')->on('trading');
            $table->foreign('idMarket')->references('id')->on('market');

            $table->unsignedInteger('quantity')->default(0)->change();
            $table->unsignedInteger('deliveryTime')->default(0)->change();
            $table->unsignedInteger('transportShips')->default(0)->change();


        });

        Schema::create('safe', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idPlanetCreator')->nullable();

            $table->unsignedBigInteger('idPlanetSale')->nullable();
            $table->unsignedBigInteger('idPlanetPurch')->nullable();
            $table->unsignedBigInteger('idTrading')->nullable();
            ;
            $table->unsignedBigInteger('idMarket')->nullable();
            $table->unsignedBigInteger('quantity'); //
            $table->boolean('loaded')->default(true);


            $table->double('price', 8, 3); //
            $table->double('total', 8, 3); //
            $table->double('distance', 11, 3); //
            $table->unsignedBigInteger('deliveryTime'); //

            $table->boolean('status')->default(true);
            $table->char('type', 1); //
            $table->timestamp('createdAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable();
            $table->string('resource', 20);
            $table->string('currency', 20);
            $table->char('step', 1)->default('I'); //


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

        DB::unprepared('
                CREATE FUNCTION calc_distancia(idPlaneta1 INT, idPlaneta2 INT)
                RETURNS INT
                BEGIN
                    DECLARE regiao1 INT;
                    DECLARE regiao2 INT;
                    DECLARE quadrant1 INT;
                    DECLARE quadrant2 INT;
                    DECLARE position1 INT;
                    DECLARE position2 INT;
                    DECLARE diffRegiao INT;
                    DECLARE diffQuadrant INT;
                    DECLARE diffPosition INT;
                    DECLARE distancia INT;

                    SELECT ASCII(region) INTO regiao1 FROM planets WHERE id = idPlaneta1;
                    SELECT ASCII(region) INTO regiao2 FROM planets WHERE id = idPlaneta2;
                    SELECT CAST(SUBSTRING(quadrant, 2, 4) AS SIGNED) INTO quadrant1 FROM planets WHERE id = idPlaneta1;
                    SELECT CAST(SUBSTRING(quadrant, 2, 4) AS SIGNED) INTO quadrant2 FROM planets WHERE id = idPlaneta2;
                    SELECT `position` INTO position1 FROM planets WHERE id = idPlaneta1;
                    SELECT `position` INTO position2 FROM planets WHERE id = idPlaneta2;

                    SET diffRegiao = ABS(regiao1 - regiao2);
                    SET diffQuadrant = ABS(quadrant1 - quadrant2);
                    SET diffPosition = ABS(position1 - position2);
                    SET distancia = (diffRegiao * 100) + (diffQuadrant * 10) + diffPosition;

                    RETURN distancia;
                END
        ');


    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::unprepared('DROP FUNCTION IF EXISTS calc_distancia');

    }
};