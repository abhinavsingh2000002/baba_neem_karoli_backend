<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DistributorController\DistributorProductOrderController;
use App\Http\Controllers\Api\DistributorController\DistributorBillController;
use App\Http\Controllers\Api\DistributorController\DistributorOrderController;
use App\Http\Controllers\Api\DistributorController\DistributorLaserController;
use App\Http\Controllers\Api\DistributorController\DistributorCredController;


Route::prefix('products')->group(function(){
    Route::post('productListing',[DistributorProductOrderController::class,'productListing'])->name('products.productListing');
    Route::post('addToCart',[DistributorProductOrderController::class,'addToCart'])->name('products.addToCart');
    Route::post('updateCartProductQuantity',[DistributorProductOrderController::class,'updateCartProductQuantity'])->name('products.updateCartProductQuantity');
    Route::post('cartProduct',[DistributorProductOrderController::class,'cartProduct'])->name('products.cartProduct');
    Route::post('removeCartProduct',[DistributorProductOrderController::class,'removeCartProduct'])->name('products.removeCartProduct');
    Route::post('orderPlaced',[DistributorProductOrderController::class,'orderPlaced'])->name('products.orderPlaced');

});

Route::prefix('bills')->group(function(){
    Route::post('billListing',[DistributorBillController::class,'billListing'])->name('bills.billListing');
    Route::post('billDetailListing',[DistributorBillController::class,'billDetailListing'])->name('bills.billDetaillisting');
    Route::post('invoicePdf',[DistributorBillController::class,'invoicePdf'])->name('bills.invoicePdf');
});

Route::prefix('orders')->group(function(){
    Route::post('orderListing',[DistributorOrderController::class,'orderListing'])->name('orders.orderListing');
    Route::post('orderDetailListing',[DistributorOrderController::class,'orderDetailListing'])->name('orders.orderDetailListing');
});

Route::prefix('laser')->group(function(){
    Route::post('laserListing',[DistributorLaserController::class,'laserListing'])->name('laser.laserListing');
});

Route::prefix('cred')->group(function(){
    Route::post('credListing',[DistributorCredController::class,'credListing'])->name('cred.laserListing');
});


