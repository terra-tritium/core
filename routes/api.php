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
use App\Http\Controllers\MessegeController;
use App\Http\Controllers\FactoryController;
use App\Http\Controllers\AliancesController;
use App\Http\Controllers\TradingController;
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

Route::controller(AuthController::class)->group(function () {
    Route::post('/user/login','createToken');
    Route::get('/user/logout','logout');
    Route::post('/user/forgot-password','sendLink');
    Route::post('/user/reset-password','resetPassword')->name('password.reset');
    Route::post('/gerar','gerar'); // @todo Geracao de posicao dos quadrantes (remover antes de ir para producao)
});

Route::controller(CountryController::class)->group(function () {
    Route::get('/country/list', 'list');
});

Route::controller(PlayerController::class)->group(function () {
    Route::post('/player/register', 'register');
    Route::get('/player/name/{userid}', 'getNameUser');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(PlayerController::class)->group(function () {
        Route::get('/player/show', 'show');
        Route::get('/player/details/{id}', 'getDetails');
        Route::post('/player/new', 'register');
        Route::post('/player/list-name/{id}', 'getNameUser');
    });

    Route::controller(BuildController::class)->group(function () {
        Route::get('/build/list', 'list');
        Route::get('/build/availables/{planet}', 'availables');
        Route::get('/building/list/{planet}', 'listBildings');
        Route::post('/build/plant', 'plant');
        Route::post('/build/up', 'upgrade');
        Route::post('/build/workers', 'workers');
        Route::get('/build/requires/{build}', 'requires');
        Route::get('/build/require/{build}/{level}', 'require');
    });

    Route::controller(FactoryController::class)->group(function () {
        Route::post('/factory/energy/{planet}/{qtd}', 'energy');
    });

    Route::controller(PlanetController::class)->group(function () {
        Route::get('/planet/list', 'list');
        Route::get('/planet/show/{id}', 'show');
        Route::get('/planet/{quadrant}/{position}', 'find');
        Route::put('/planet/edit/{planet}', 'update');
    });

    Route::controller(UnitController::class)->group(function () {
        Route::get('/unit/list', 'list');
    });

    Route::controller(TroopController::class)->group(function () {
        Route::post('/troop/production/{planet}', 'production');
        Route::get('/troop/production/{planet?}', 'producing');
    });

    Route::controller(RankingController::class)->group(function () {
        Route::get('/ranking/players/{type}', 'players');
        Route::get('/ranking/aliances/{type}', 'aliances');
    });

    Route::controller(ResearchController::class)->group(function () {
        Route::get('/research/list', 'list');
        Route::get('/researched', 'researched');
        Route::post('/research/start/{code}/{sincronize?}', 'start');
        Route::post('/research/done/{code}', 'done');
        Route::get('/research/status/{code}', 'getStatus');
    });

    Route::controller(GameModeController::class)->group(function () {
        Route::get('/mode/list', 'list');
        Route::post('/mode/change/{code}', 'change');
    });

    Route::controller(TravelController::class)->group(function () {
        Route::get('/travel/list', 'list');
        Route::get('/travel/current', 'current');
        Route::post('/travel/start', 'start');
        Route::post('/travel/back', 'back');
    });

    Route::controller(BattleController::class)->group(function () {
        // Route::get('/battle/attackmode/list', 'attackModeList');
        // Route::get('/battle/defensemode/list', 'defenseModeList');
        Route::post('/battle/attackmode/{option}', 'changeAttackMode');
        Route::post('/battle/defensemode/{option}', 'changeDefenseMode');
        Route::get('/battle/start/{defense}/{planet}', 'start');
        Route::get('/battle/view/{id}', 'view');
        Route::get('/battle/stages/{id}', 'stages');
    });

    Route::controller(QuadrantController::class)->group(function () {
        Route::get('/quadrant/show/{code}', 'show');
        Route::get('/quadrant/map/{region}', 'map');
        Route::get('/quadrant/planets/{quadrant}', 'planets');
    });

    Route::controller(MessegeController::class)->group(function () {
        Route::get('/messege/all','getAll');
        Route::get('/messege/all-sender/{id}', 'getAllByUserSender');
        Route::get('/messege/all-recipient', 'getAllByUserRecipient');
        Route::post('/messege/read','readMessege');
        Route::get('/messege/list', 'list');
        Route::get('/message/send-for-recipient/{senderid}', 'getAllMessageSenderForRecipent');
        Route::get('/message/count' ,'getCountMessageNotRead');

        Route::get('/messege/not-read', 'getAllMessegeNotRead');
        Route::get('/message/getSenders', 'getSenders');
        Route::get('/messages/conversation/{senderid}', 'getConversation');
        Route::post('/messege/new','newMessege');
        Route::get('/messege/lastmsg-sender/{senderid}','getLastMessageNotReadBySender');

    });

    Route::controller(RankingController::class)->group(function (){
        Route::get('/ranking/players', [RankingController::class, 'getPlayerRanking']);
        Route::get('/ranking/aliances', [RankingController::class, 'getAlianceRanking']);
    });

    Route::controller(AliancesController::class)->group(function () {
        Route::post('/aliances/create',  'create');
        Route::put('/aliances/edit/{id}', 'update');
        Route::get('/aliances/list',  'index');
        Route::delete('/aliances/delete/{id}',  'destroy');
        Route::put('/aliances/update-avatar/{id}',  'updateAvatar');
    });

    Route::controller(TradingController::class)->group(function (){
        Route::get('/trading/{type}', 'getAllTradingByMarketResource');
    });


});
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


