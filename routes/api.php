<?php

use App\Http\Controllers\BuildController;
use App\Http\Controllers\NFTEffectsController;
use App\Http\Controllers\PlanetController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\TroopController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\GameModeController;
use App\Http\Controllers\TravelController;
use App\Http\Controllers\CombatController;
use App\Http\Controllers\QuadrantController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\AliancesController;
use App\Http\Controllers\EspionadeController;
use App\Http\Controllers\TradingController;
use App\Http\Controllers\NFTController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ShipController;
use App\Http\Controllers\FleetController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\ChallangeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'user', 'middleware' => 'throttle:30,1'], function () {
    Route::post('/login', [AuthController::class, 'createToken']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/forgot-password', [AuthController::class, 'sendLink']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::get('/email-verify/{id}/{hash}', [AuthController::class,'verifyEmail'])->name('verification.verify');
    Route::post('/verification-notification/{email}',[AuthController::class,'sendLinkVerifyEmailRestister'])->middleware(['throttle:6,1'])->name('verification.send');
});

Route::group(['prefix' => 'country', 'middleware' => 'throttle:30,1'], function () {
    Route::get('/list', [CountryController::class, 'list']);
});

Route::group(['prefix' => 'server', 'middleware' => 'throttle:30,1'], function () {
    Route::get('/list', [ServerController::class, 'list']);
});

Route::group(['prefix' => 'player', 'middleware' => 'throttle:30,1'], function () {
    Route::post('/register', [PlayerController::class, 'register']);
    Route::get('/name/{userid}', [PlayerController::class, 'getNameUser']);
});

/**
 * Rota Publica, ping
 */
Route::get('/ping',function(){
    return "pong";
})->middleware(['throttle:10,1']);

/**
 * @todo remover endpoint antes de enviar para produção
 */
Route::get('/generate-token', [AuthController::class, 'generateToken']);


/**
 * @todo retirar o /all
 */
Route::middleware(['auth:sanctum','verified'])->group(function () {
    Route::group(['prefix' => 'player', 'middleware' => 'throttle:240,1'], function () {
        Route::get('/show', [PlayerController::class, 'show']);
        Route::get('/details/{id}', [PlayerController::class, 'getDetails']);
        Route::post('/new', [PlayerController::class, 'register']);
        Route::post('/list-name/{id}', [PlayerController::class, 'getNameUser']);
        Route::post('/change-name', [PlayerController::class, 'changeName']);
        Route::get('/all',[PlayerController::class, 'showAll']);
    });

    Route::group(['prefix' => 'build', 'middleware' => 'throttle:240,1'], function () {
        Route::get('/list', [BuildController::class, 'list']);
        Route::get('/availables/{planet}', [BuildController::class, 'availables']);
        Route::post('/plant', [BuildController::class, 'plant']);
        Route::post('/up', [BuildController::class, 'upgrade']);
        Route::post('/workers', [BuildController::class, 'workers']);
        Route::get('/requires/{build}', [BuildController::class, 'requires']);
        Route::get('/require/{build}/{level}', [BuildController::class, 'require']);
        Route::post('/demolish/{build}', [BuildController::class, 'demolish']);
    });

    Route::get('/building/list/{planet}', [BuildController::class, 'listBildings'])->middleware(['throttle:240,1']);

    Route::group(['prefix' => 'factory', 'middleware' => 'throttle:240,1'], function () {
        Route::post('/humanoid/create/{planet}/{qtd}', [FactoryController::class, 'createHumanoid']);
        Route::post('/transportship/create/{planet}/{qtd}', [FactoryController::class, 'createTransportShip']);

    });

    Route::group(['prefix' => 'planet', 'middleware' => 'throttle:240,1'], function () {
        Route::get('/list', [PlanetController::class, 'list']);
        Route::get('/show/{id}', [PlanetController::class, 'show']);
        Route::get('/{quadrant}/{position}', [PlanetController::class, 'find']);
        Route::put('/edit/{planet}', [PlanetController::class, 'update']);
        Route::get('/calcule-distance/{origin}/{destiny}', [PlanetController::class, 'calculeDistance']);
    });

    Route::group(['prefix' => 'unit', 'middleware' => 'throttle:240,1'], function () {
        Route::get('/list', [UnitController::class, 'list']);
    });
    Route::group(['prefix' => 'ship', 'middleware' => 'throttle:240,1'], function () {
        Route::get('/list', [ShipController::class, 'list']);
    });

    Route::group(['prefix' => 'challange', 'middleware' => 'throttle:240,1'], function () {
        Route::post('/start/{from}/{to}', [ChallangeController::class, 'startMission']);
        Route::post('/convert/{planet}', [ChallangeController::class, 'convert']);
        Route::get('/mission/{planet}', [ChallangeController::class, 'mission']);
    });

    Route::group(['prefix' => 'troop', 'middleware' => 'throttle:240,1'],function () {
        Route::post('/production/{planet}', [TroopController::class, 'production']);
        Route::get('/production/{planet?}', [TroopController::class, 'producing']);
        Route::get('/{planet}', [TroopController::class, 'list']);
    });

    Route::group(['prefix' => 'fleet', 'middleware' => 'throttle:240,1'], function () {
        Route::post('/production/{planet}', [FleetController::class, 'production']);
        Route::get('/production/{planet?}', [FleetController::class, 'producing']);
        Route::get('/{planet}', [FleetController::class, 'list']);
    });

    Route::group(['prefix' => 'ranking', 'middleware' => 'throttle:240,1'],function () {
        Route::get('/players/{type}', [RankingController::class, 'players']);
        Route::get('/aliances/{type}', [RankingController::class, 'aliances']);
    });

    Route::group(['prefix' => 'research', 'middleware' => 'throttle:240,1'], function () {
        Route::get('/list', [ResearchController::class, 'list']);
        Route::post('/laboratory/config/{planet}/{power}', [ResearchController::class, 'laboratoryConfig']);
        Route::post('/buy/{code}', [ResearchController::class, 'buyResearch']);
    });

    Route::get('/researched', [ResearchController::class, 'researched'])->middleware(['throttle:240,1']);;

    Route::group(['prefix' => 'mode', 'middleware' => 'throttle:240,1'], function () {
        Route::get('/list', [GameModeController::class, 'list']);
        Route::post('/change/{code}', [GameModeController::class, 'change']);
        Route::get('/effect/{planet}',[GameModeController::class, 'gameModeEffect']);
        Route::get('/effect-player/{player}',[GameModeController::class, 'gameModeEffectPlayer']);

    });

    Route::group(['prefix' => 'travel', 'middleware' => 'throttle:240,1'], function () {
        Route::get('/list', [TravelController::class, 'list']);
        Route::get('/current', [TravelController::class, 'current']);
        Route::get('/missions/{action}', [TravelController::class, 'missions']);
        Route::post('/start', [TravelController::class, 'start']);
        Route::post('/back', [TravelController::class, 'back']);
        Route::put('/cancel/{travel}', [TravelController::class, 'cancel']);
        Route::post('/spey', [TravelController::class, 'speyMission']);
    });

    Route::group(['prefix' => 'combat', 'middleware' => 'throttle:240,1'], function () {
        Route::post('/attackmode/{option}', [CombatController::class, 'changeAttackMode']);
        Route::post('/defensemode/{option}', [CombatController::class, 'changeDefenseMode']);
        Route::get('/start/{defense}/{planet}', [CombatController::class, 'start']);
        Route::get('/view/{id}', [CombatController::class, 'view']);
        Route::get('/stages/{id}', [CombatController::class, 'stages']);
        Route::get('/list', [CombatController::class, 'list']);
        Route::get('/strategy/list/', [CombatController::class, 'listStrategy']);
        Route::get('/strategy/selected/{planet}',[CombatController::class, 'strategiesSelectedPlanet']);
        Route::put('/strategy/{planet}/{type}/{newStrategy}',[CombatController::class, 'changeStrategy']);
        Route::get("/check-number-planets",[CombatController::class, 'checkNumberOfPlanets']);
        Route::put("/colonizer/{planet}",[CombatController::class, "colonizePlanet"]);
        Route::get("/available-ship",[CombatController::class, "availableShip"]);
        Route::post("/actionmode",[CombatController::class, "actionMode"]);
        Route::get("/available-resources/{planet}",[CombatController::class, "availableResources"]);
        Route::get("/figthers/{combat}",[CombatController::class, "figthers"]);
        Route::get("/current/{combat}",[CombatController::class, "current"]);
        Route::get("/arrival-planet/{from}",[CombatController::class, "arrivalPlanet"]);
        Route::get("/stage/{combatId}",[CombatController::class,"calculateStage"]);
        Route::post("/space-leave/{combatId}", [CombatController::class, "spaceLeave"]);
        //sendresource
        Route::post('/sendresource', [CombatController::class, 'sendResource']);
    });

    Route::group(['prefix' => 'quadrant', 'middleware' => 'throttle:240,1'], function () {
        Route::get('/show/{code}/{planet?}', [QuadrantController::class, 'show']);
        Route::get('/map/{region}', [QuadrantController::class, 'map']);
        Route::get('/planets/{quadrant}', [QuadrantController::class, 'planets']);
        Route::get('/calule-distancte/{origin}/{destiny}', [QuadrantController::class, 'calcDistancePlants']);
    });

    Route::group(['prefix' => 'message', 'middleware' => 'throttle:240,1'], function () {
        Route::get('/all', [MessageController::class, 'getAll']);
        Route::get('/all-sender/{id}', [MessageController::class, 'getAllByUserSender']);
        Route::get('/all-recipient', [MessageController::class, 'getAllByUserRecipient']);
        Route::post('/read', [MessageController::class, 'readMessage']);
        Route::get('/list', [MessageController::class, 'list']);
        Route::get('/send-for-recipient/{senderid}', [MessageController::class, 'getAllMessageSenderForRecipent']);
        Route::get('/unread', [MessageController::class, 'getCountConversationUnread']);

        Route::get('/not-read', [MessageController::class, 'getAllMessageNotRead']);
        Route::get('/getSenders', [MessageController::class, 'getSenders']);
        Route::get('/conversation/{senderid}', [MessageController::class, 'getConversation']);
        Route::post('/new', [MessageController::class, 'newMessage']);
        Route::get('/lastmsg-sender/{senderid}', [MessageController::class, 'getLastMessageNotReadBySender']);

        Route::get('/search-usuer/{string}', [MessageController::class, 'searchUser']);
        Route::get('/owner/{id}', [MessageController::class, 'getOwnerPlanet']);

    });

    Route::group(['prefix' => 'ranking', 'middleware' => 'throttle:240,1'], function () {
        Route::get('/players', [RankingController::class, 'getPlayerRanking']);
        Route::get('/aliances', [RankingController::class, 'getAlianceRanking']);
    });

    Route::group(['prefix' => 'logs', 'middleware' => 'throttle:240,1'], function () {
        Route::get('/logs', [LogController::class, 'logs']);
        Route::post('/create', [LogController::class, 'create']);
        Route::put('/update/{id}',[LogController::class, 'update']);
        Route::get('/processjob/{type}', [LogController::class, 'jobSleep']);
    });

    Route::group(['prefix' => 'aliance', 'middleware' => 'throttle:240,1'], function () {
        Route::get('/list', [AliancesController::class, 'index']);
        Route::post('/create', [AliancesController::class, 'create']);
        Route::put('/edit/{id}', [AliancesController::class, 'update']);
        Route::delete('/delete/{idAliance}', [AliancesController::class, 'destroy']);

        Route::put('/updatelogo/{id}', [AliancesController::class, 'updateLogo']);
        Route::post('/join', [AliancesController::class,'joinAliance']);
        Route::post('/request', [AliancesController::class,'handlePlayerRequest']);
        Route::post('/leave',[AliancesController::class,'leaveAliance']);

        Route::post('/kickplayer', [AliancesController::class, 'kickPlayer']);
        Route::get('/{alianceId}/players', [AliancesController::class,'listPlayers']);
        Route::get('/my-aliance', [AliancesController::class,'myAliance']);
        Route::get('/myaliance/details', [AliancesController::class, 'alianceDetailsCreated']);
        Route::get('/members/{alianceId}', [AliancesController::class,'listMembers']);
        Route::get('/members/pending/{alianceId}',[AliancesController::class, 'listMembersPending']);
        Route::patch('member/remove/{memberId}', [AliancesController::class, 'removeMember']);
        Route::get('/logos',[AliancesController::class, 'allLogos']);
        Route::put('/member/update-request/{idMemberAliance}/{action}',[AliancesController::class, 'updateRequestMember']);
        Route::get('/available-name/{name}',[AliancesController::class, 'getAvailableName']);
        Route::put('/exit/{alianceId}',[AliancesController::class, 'exit']);
        Route::put('/cancel-request',[AliancesController::class, 'cancelRequest']);
        Route::get('/ranks',[AliancesController::class, 'getRanks']);
        Route::get('/members-rank/{idAliance}',[AliancesController::class, 'getMembersRank']);
        Route::put('/change-rank/{idRank}/{idMember}/{idAliance}',[AliancesController::class, 'changeRankMember']);
        Route::put('/relinquish-rank/{idAliance}/{idMember}',[AliancesController::class, 'deixarRank']);
        Route::get('/member/units/{playerid}/{type}',[AliancesController::class, 'getUnitsPlayer']);
        /** Chat Alianca */
        Route::get('/list-aliance-chat',[AliancesController::class, 'listAlianceForChat']);
        Route::post('/chat',[AliancesController::class, 'newMessageGroup']);/*  */
        Route::get('/chat/{idAliance}',[AliancesController::class, 'getMessagesGroup']);
        Route::put('/chat/{idMessage}',[AliancesController::class, 'delMessage']);
        Route::post('/chat-aliance',[AliancesController::class, 'newMessageAliance']);
        Route::get('/chat-with-aliance/{destino}',[AliancesController::class, 'getMessageWithAliance']);

        Route::get('/search-usuer/{string}',[AliancesController::class,'searchUser']);
        Route::post('/invite',[AliancesController::class, 'invite']);
        Route::get('/invite',[AliancesController::class, 'receivedInvitations']);
        Route::post('/invite-accepted',[AliancesController::class, 'acceptInvite']);

        Route::get("/scores", [AliancesController::class, 'getScoresAliance']);
    });

    Route::group(['prefix' => 'trading', 'middleware' => 'throttle:240,1'], function () {
        Route::get('/all/{resource}/{type}', [TradingController::class, 'getAllTradingByMarketResource']);
        Route::get('/myresources', [TradingController::class, 'getMyResources']);
        Route::post('/new-sale', [TradingController::class, 'tradingNewSale']);
        Route::post('/new-purch',[TradingController::class, 'tradingNewPurchase']);
        Route::get('/my-history/{planet}/{id}', [TradingController::class, 'getAllOrderByPlanet']);
        Route::patch('/cancel/{planet}/{id}', [TradingController::class, 'cancelOrder']);
        Route::get('/trading-process/{id}',[TradingController::class, 'getTradingProcess']);
        Route::post('/finish', [TradingController::class, 'finishTrading']);
        Route::get('/safe/conclued',[TradingController::class, 'verificaTradeConcluidoSafe'] );
        Route::get('/last-trading',[TradingController::class, 'lastTrading'] );
        Route::patch('/buy-freighter/{planetId}',[TradingController::class, 'buyFreighter']);
        Route::get('/player/resource/{planet}',[TradingController::class, 'getPlayerResource']);

    });

    Route::group(['prefix' => 'nft', 'middleware' => 'throttle:240,1'], function () {
        Route::post('/config/{slot}/{code}', [NFTController::class, 'config']);
        Route::get('/config/get', [NFTController::class, 'get']);
    });

    Route::group(['prefix' => 'nft-effect', 'middleware' => 'throttle:240,1'], function () {
        Route::get('/get', [NFTEffectsController::class, 'getNftEffects']);
    });
    Route::group(['prefix' => 'espionage', 'middleware' => 'throttle:240,1'], function () {
        Route::get('/list', [EspionadeController::class, 'list']);
    });

});
