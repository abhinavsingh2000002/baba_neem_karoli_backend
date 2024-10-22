<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\DriverMiddleware;
use App\Http\Controllers\DriverController\DriverTaskController;
use App\Http\Controllers\DriverController\DriverCredController;
use App\Http\Controllers\DriverController\DriverOrderReportController;

Route::middleware(DriverMiddleware::class)->group(function(){
    // Driver Task Routes Start-------------------------------------------------------------
    Route::prefix('driver_task')->group(function(){
        Route::get('index',[DriverTaskController::class,'index'])->name('driver_task.index');
        Route::get('listing',[DriverTaskController::class,'listing'])->name('driver_task.listing');
        Route::get('detailListing/{id}',[DriverTaskController::class,'detailListing'])->name('driver_task.detailListing');
        Route::post('approve',[DriverTaskController::class,'approve'])->name('driver_task.approve');
    });
    // Driver Task Routes End-------------------------------------------------------------

     // Driver CRed OUT Routes Start-------------------------------------------------------------
     Route::prefix('driver_cred')->group(function(){
        Route::get('index',[DriverCredController::class,'index'])->name('driver_cred.index');
        Route::get('listing',[DriverCredController::class,'listing'])->name('driver_cred.listing');
        Route::get('add',[DriverCredController::class,'add'])->name('driver_cred.add');
        Route::post('create',[DriverCredController::class,'create'])->name('driver_cred.create');
    });
    // Driver Out Routes End-------------------------------------------------------------


     // Order Report Routes Start--------------------------------------------------------------------------
     Route::prefix('driver_report')->group(function(){
        Route::get('index',[DriverOrderReportController::class,'index'])->name('driver_report.index');
        Route::get('listing',[DriverOrderReportController::class,'listing'])->name('driver_report.listing');
        Route::get('reportpdf',[DriverOrderReportController::class,'reportpdf'])->name('driver_report.reportpdf');
        Route::get('reportExcel',[DriverOrderReportController::class,'reportExcel'])->name('driver_report.reportExcel');
    });
    // Order Report Routes End--------------------------------------------------------------------------


});
