<?php

use App\Http\Controllers\BuildController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\PlanetController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\TroopController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\GameModeController;
use App\Http\Controllers\TravelController;
use App\Http\Controllers\BattleController;
use App\Http\Controllers\QuadrantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\ResetarSenhaController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\AliancesController;
use App\Http\Controllers\TradingController;
use App\Http\Controllers\NFTController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\RotinasController;
use Illuminate\Http\Request;
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

Route::prefix('user')->group(function () {
    Route::post('/login', [AuthController::class, 'createToken']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/forgot-password', [AuthController::class, 'sendLink']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
});
Route::post('/gerar',[AuthController::class, 'gerar']); // @todo Geracao de posicao dos quadrantes (remover antes de ir para producao)

Route::group(['prefix' => 'country'], function () {
    Route::get('/list', [CountryController::class, 'list']);
});

Route::group(['prefix' => 'player'], function () {
    Route::post('/register', [PlayerController::class, 'register']);
    Route::get('/name/{userid}', [PlayerController::class, 'getNameUser']);
});

/**
 * @todo retirar o /all
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::group(['prefix' => 'player'], function () {
        Route::get('/show', [PlayerController::class, 'show']);
        Route::get('/details/{id}', [PlayerController::class, 'getDetails']);
        Route::post('/new', [PlayerController::class, 'register']);
        Route::post('/list-name/{id}', [PlayerController::class, 'getNameUser']);
        Route::post('/change-name', [PlayerController::class, 'changeName']);
        Route::get('/all',[PlayerController::class, 'showAll']);
    });

    Route::group(['prefix' => 'build'], function () {
        Route::get('/list', [BuildController::class, 'list']);
        Route::get('/availables/{planet}', [BuildController::class, 'availables']);
        Route::post('/plant', [BuildController::class, 'plant']);
        Route::post('/up', [BuildController::class, 'upgrade']);
        Route::post('/workers', [BuildController::class, 'workers']);
        Route::get('/requires/{build}', [BuildController::class, 'requires']);
        Route::get('/require/{build}/{level}', [BuildController::class, 'require']);
        Route::post('/demolish/{build}', [BuildController::class, 'demolish']);
    });

    Route::get('/building/list/{planet}', [BuildController::class, 'listBildings']);

    Route::group(['prefix' => 'factory'], function () {
        Route::post('/humanoid/create/{planet}/{qtd}', [FactoryController::class, 'createHumanoid']);
    });

    Route::prefix('planet')->group(function () {
        Route::get('/list', [PlanetController::class, 'list']);
        Route::get('/show/{id}', [PlanetController::class, 'show']);
        Route::get('/{quadrant}/{position}', [PlanetController::class, 'find']);
        Route::put('/edit/{planet}', [PlanetController::class, 'update']);
    });

    Route::prefix('unit')->group(function () {
        Route::get('/list', [UnitController::class, 'list']);
    });

    Route::prefix('troop')->group(function () {
        Route::post('/production/{planet}', [TroopController::class, 'production']);
        Route::get('/production/{planet?}', [TroopController::class, 'producing']);
        Route::get('/{planet}', [TroopController::class, 'list']);
    });

    Route::prefix('ranking')->group(function () {
        Route::get('/players/{type}', [RankingController::class, 'players']);
        Route::get('/aliances/{type}', [RankingController::class, 'aliances']);
    });

    Route::prefix('research')->group(function () {
        Route::get('/list', [ResearchController::class, 'list']);
        Route::post('/laboratory/config/{planet}/{power}', [ResearchController::class, 'laboratoryConfig']);
        Route::post('/buy/{code}', [ResearchController::class, 'buyResearch']);
    });

    Route::get('/researched', [ResearchController::class, 'researched']);

    Route::prefix('mode')->group(function () {
        Route::get('/list', [GameModeController::class, 'list']);
        Route::post('/change/{code}', [GameModeController::class, 'change']);
    });

    Route::prefix('travel')->group(function () {
        Route::get('/list', [TravelController::class, 'list']);
        Route::get('/current', [TravelController::class, 'current']);
        Route::get('/missions/{action}', [TravelController::class, 'missions']);
        Route::post('/start', [TravelController::class, 'start']);
        Route::post('/back', [TravelController::class, 'back']);
    });

    Route::prefix('battle')->group(function () {
        Route::post('/attackmode/{option}', [BattleController::class, 'changeAttackMode']);
        Route::post('/defensemode/{option}', [BattleController::class, 'changeDefenseMode']);
        Route::get('/start/{defense}/{planet}', [BattleController::class, 'start']);
        Route::get('/view/{id}', [BattleController::class, 'view']);
        Route::get('/stages/{id}', [BattleController::class, 'stages']);
        Route::get('/list', [BattleController::class, 'list']);
    });

    Route::prefix('quadrant')->group(function () {
        Route::get('/show/{code}/{planet?}', [QuadrantController::class, 'show']);
        Route::get('/map/{region}', [QuadrantController::class, 'map']);
        Route::get('/planets/{quadrant}', [QuadrantController::class, 'planets']);
    });

    Route::prefix('message')->group(function () {
        Route::get('/all', [MessageController::class, 'getAll']);
        Route::get('/all-sender/{id}', [MessageController::class, 'getAllByUserSender']);
        Route::get('/all-recipient', [MessageController::class, 'getAllByUserRecipient']);
        Route::post('/read', [MessageController::class, 'readMessage']);
        Route::get('/list', [MessageController::class, 'list']);
        Route::get('/send-for-recipient/{senderid}', [MessageController::class, 'getAllMessageSenderForRecipent']);
        Route::get('/count', [MessageController::class, 'getCountMessageNotRead']);

        Route::get('/not-read', [MessageController::class, 'getAllMessageNotRead']);
        Route::get('/getSenders', [MessageController::class, 'getSenders']);
        Route::get('/conversation/{senderid}', [MessageController::class, 'getConversation']);
        Route::post('/new', [MessageController::class, 'newMessage']);
        Route::get('/lastmsg-sender/{senderid}', [MessageController::class, 'getLastMessageNotReadBySender']);

        Route::get('/search-usuer/{string}', [MessageController::class, 'searchUser']);
    });

    Route::prefix('ranking')->group(function () {
        Route::get('/players', [RankingController::class, 'getPlayerRanking']);
        Route::get('/aliances', [RankingController::class, 'getAlianceRanking']);
    });

    Route::prefix('logs')->group(function () {
        Route::get('/logs', [LogController::class, 'logs']);
        Route::post('/create', [LogController::class, 'create']);
    });

    Route::prefix('aliance')->group(function () {
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

        Route::get("/scores", [AliancesController::class, 'getScoresAliance']);
    });

    Route::prefix('trading')->group(function () {
        Route::get('/all/{resource}/{type}/{orderby?}/{column?}', [TradingController::class, 'getAllTradingByMarketResource']);
        Route::get('/myresources', [TradingController::class, 'getMyResources']);
        Route::post('/new-sale', [TradingController::class, 'tradingNewSale']);
        Route::post('/new-purch',[TradingController::class, 'tradingNewPurchase']);
        Route::get('/my-history/{id}', [TradingController::class, 'getAllOrdersPlayer']);
        Route::patch('/cancel/{id}', [TradingController::class, 'cancelOrder']);
        Route::get('/trading-process/{id}',[TradingController::class, 'getTradingProcess']);
        Route::post('/finish', [TradingController::class, 'finishTrading']);
        Route::get('/safe/conclued',[TradingController::class, 'verificaTradeConcluidoSafe'] );
        Route::get('/last-trading',[TradingController::class, 'lastTrading'] );
        Route::patch('/buy-freighter/{planetId}',[TradingController::class, 'buyFreighter']);

    });

    Route::prefix('nft')->group(function () {
        Route::post('/config/{slot}/{code}', [NFTController::class, 'config']);
        Route::get('/config/get', [NFTController::class, 'get']);
    });

 
});
/**
 * @todo retirar a chamada da rotina
 */
Route::get('/rotinas',[RotinasController::class, 'exec']);
/**
 * Rota Publica, ping
 */
Route::get('/ping',function(){
    return "pong";
});

/**
 * @todo remover endpoint antes de enviar para produção
 */
Route::get('/generate-token', [AuthController::class, 'generateToken']);


