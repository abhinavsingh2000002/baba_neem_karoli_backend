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
    Route::post('logout',[AuthController::class,'logout']);
    Route::post('editProfile',[AuthController::class,'editProfile']);
});

Route::prefix('user')->group(function(){
    Route::post('/register',[UserRegisterController::class,'Register']);
    Route::post('/listing',[UserRegisterController::class,'Listing']);
    Route::post('/detaillisting',[UserRegisterController::class,'DetailListing']);
});

require __DIR__.'/ApiRoutes/DistributorRoutes.php';
require __DIR__.'/ApiRoutes/DriverRoutes.php';
require __DIR__.'/ApiRoutes/AdminRoutes.php';
