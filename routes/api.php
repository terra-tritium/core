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
//use App\Http\Controllers\AuthController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::controller(AuthController::class)->group(function () {
//     Route::post('/create-token','createToken');
// });

// Route::middleware('auth:sanctum')->group(function () {  

// });

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

Route::controller(CountryController::class)->group(function () {
    Route::get('/country/list', 'list');
});

Route::controller(PlanetController::class)->group(function () {
    Route::get('/planet/list/{address}', 'list');
});

Route::controller(PlayerController::class)->group(function () {
    Route::get('/player/show/{address}', 'show');
    Route::post('/player/register', 'register');
});

Route::controller(UnitController::class)->group(function () {
    Route::get('/unit/list', 'list');
});

Route::controller(TroopController::class)->group(function () {
    Route::post('/troop/production/{address}/{planet}', 'production');
});

Route::controller(RankingController::class)->group(function () {
    Route::get('/ranking/players/{address}/{type}', 'players');
    Route::get('/ranking/aliances/{address}/{type}', 'aliances');
});

Route::controller(ResearchController::class)->group(function () {
    Route::get('/research/list', 'list');
    Route::get('/researched/{address}', 'researched');
    Route::post('/research/start/{address}/{code}', 'start');
    Route::post('/research/done/{address}/{code}', 'done');
});

Route::controller(GameModeController::class)->group(function () {
    Route::get('/mode/list', 'list');
    Route::post('/mode/change/{address}/{code}', 'change');
});

Route::controller(TravelController::class)->group(function () {
    Route::get('/travel/list/{address}', 'list');
    Route::get('/travel/current/{address}', 'current');
    Route::post('/travel/start/{address}', 'start');
    Route::post('/travel/back/{address}', 'back');
});

Route::controller(BattleController::class)->group(function () {
    Route::get('/battle/attackmode/list', 'attackModeList');
    Route::get('/battle/defensemode/list', 'defenseModeList');
    Route::post('/battle/attackmode/{address}/{option}', 'changeAttackMode');
    Route::post('/battle/defensemode/{address}/{option}', 'changeDefenseMode');
    Route::get('/battle/start', 'start');
    Route::get('/battle/view/{id}', 'view');
});