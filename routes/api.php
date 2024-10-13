<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserRegisterController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function(){
    Route::post('get_connection_id',[AuthController::class,'get_connection_id']);
    Route::post('login',[AuthController::class,'login']);
    ROute::post('logout',[AuthController::class,'logout']);
});

Route::prefix('user')->group(function(){
    Route::post('/register',[UserRegisterController::class,'Register']);
    Route::post('/listing',[UserRegisterController::class,'Listing']);
    Route::post('/detaillisting',[UserRegisterController::class,'DetailListing']);
});
