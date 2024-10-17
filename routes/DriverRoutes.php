<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\DriverMiddleware;
use App\Http\Controllers\DriverController\DriverTaskController;

Route::middleware(DriverMiddleware::class)->group(function(){
    // Driver Task Routes Start-------------------------------------------------------------
    Route::prefix('driver_task')->group(function(){
        Route::get('index',[DriverTaskController::class,'index'])->name('driver_task.index');
        Route::get('listing',[DriverTaskController::class,'listing'])->name('driver_task.listing');
        Route::get('detailListing/{id}',[DriverTaskController::class,'detailListing'])->name('driver_task.detailListing');
        // Route::get('detailListing/{id}',[DriverTaskController::class,'detailListing'])->name('driver_task.detailListing');
        Route::post('approve',[DriverTaskController::class,'approve'])->name('driver_task.approve');
    });
    // Driver Task Routes End-------------------------------------------------------------


});
