<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController\AdminBillController;
use App\Http\Controllers\Api\AdminController\AdminOrderController;

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