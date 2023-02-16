<?php

use App\Http\Controllers\BuildController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\PlanetController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
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
