<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\ApiAuthController;

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

Route::prefix('/v1')->group( function() {
    Route::post('/login', [ApiAuthController::class,'login']);
    Route::post('/create-user', [ApiAuthController::class, 'create']);

    Route::middleware('auth:sanctum')->group( function() {
        Route::get('/logout', [ApiAuthController::class, 'logout']);

        Route::get('/list-user', [UserController::class, 'index']);

        Route::prefix('/gain')->group( function() {
            Route::get('/list', [InvestmentController::class, 'getGain']);
            Route::post('/create', [InvestmentController::class, 'creatGain']);
        });

        Route::prefix('/investments')->group( function() {
            Route::get('/list', [InvestmentController::class, 'index']);
            Route::post('/invest', [InvestmentController::class, 'createInvestment']);
            Route::post('/view-investment', [InvestmentController::class, 'getInvestment']);
            Route::post('/withdrawal', [InvestmentController::class, 'withdrawalInvestment']);
            Route::post('/view-all-investment', [InvestmentController::class, 'getAllInvestment']);
        });
    });
});
