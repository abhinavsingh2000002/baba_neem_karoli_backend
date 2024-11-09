<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController\AdminBillController;
use App\Http\Controllers\Api\AdminController\AdminOrderController;
use App\Http\Controllers\Api\AdminController\AdminLaserController;
use App\Http\Controllers\Api\AdminController\AdminCredController;
use App\Http\Controllers\Api\AdminController\AdminDashboardController;
use App\Http\Controllers\Api\AdminController\AdminReportController;

Route::prefix('adminDashboard')->group(function(){
    Route::post('dashboard',[AdminDashboardController::class,'dashboard'])->name('adminDashboard.dashboard');
});

Route::prefix('adminBills')->group(function(){
    Route::post('distributorList',[AdminBillController::class,'distributorList'])->name('adminBills.distributorList');
    Route::post('billListing',[AdminBillController::class,'billListing'])->name('adminBills.billListing');
    Route::post('billDetailListing',[AdminBillController::class,'billDetailListing'])->name('adminBills.billDetaillisting');
});

Route::prefix('adminOrders')->group(function(){
    Route::post('distributorList',[AdminOrderController::class,'distributorList'])->name('adminBills.distributorList');
    Route::post('orderListing',[AdminOrderController::class,'orderListing'])->name('adminOrders.orderListing');
    Route::post('orderDetailListing',[AdminOrderController::class,'orderDetailListing'])->name('adminOrders.orderDetailListing');
});

Route::prefix('adminLaser')->group(function(){
    Route::post('distributorList',[AdminLaserController::class,'distributorList'])->name('adminLaser.distributorList');
    Route::post('laserListing',[AdminLaserController::class,'laserListing'])->name('adminLaser.laserListing');
});

Route::prefix('adminCred')->group(function(){
    Route::post('distributorList',[AdminCredController::class,'distributorList'])->name('adminCred.distributorList');
    Route::post('credListing',[AdminCredController::class,'credListing'])->name('adminCred.credListing');
});

Route::prefix('adminReport')->group(function(){
    Route::post('reportListing',[AdminReportController::class,'reportListing'])->name('report.reportListing');
});
