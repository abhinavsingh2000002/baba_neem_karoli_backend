<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DriverController\DriverAllotedTaskController;
use App\Http\Controllers\Api\DriverController\DriverAddCredController;
use App\Http\Controllers\Api\DriverController\DriverReportController;



Route::prefix('allotedTask')->group(function(){
    Route::post('allotedTaskListing',[DriverAllotedTaskController::class,'allotedTaskListing'])->name('allotedTask.allotedTaskListing');
    Route::post('allotedTaskApprove',[DriverAllotedTaskController::class,'allotedTaskApprove'])->name('allotedTask.allotedTaskApprove');
});

Route::prefix('addCred')->group(function(){
    Route::post('distributorListing',[DriverAddCredController::class,'distributorListing'])->name('addCred.distributorListing');
    Route::post('credCreate',[DriverAddCredController::class,'credCreate'])->name('addCred.credCreate');
    Route::post('credListing',[DriverAddCredController::class,'credListing'])->name('addCred.credListing');
});

Route::prefix('report')->group(function(){
    Route::post('reportProductListing',[DriverReportController::class,'reportProductListing'])->name('report.reportProductListing');
    Route::post('reportListing',[DriverReportController::class,'reportListing'])->name('report.reportListing');
});
