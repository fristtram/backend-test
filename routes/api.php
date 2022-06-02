<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\UserController;

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

Route::prefix('/v1')->group( function() {

    Route::prefix('/users')->group( function() {
        Route::get('/list', [UserController::class, 'index']);
        Route::post('/create', [UserController::class, 'create']);
    });

    Route::prefix('/investments')->group( function() {
        Route::post('/invest', [InvestmentController::class, 'createInvestment']);
        Route::get('/list/{invest}/{date}', [InvestmentController::class, 'getInvestment']);
        Route::post('/withdrawal', [InvestmentController::class, 'withdrawalInvestment']);
        Route::get('/withdrawal/{user}', [InvestmentController::class, 'personInvestment']);
    });
});
