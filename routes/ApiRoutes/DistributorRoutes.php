<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DistributorController\DistributorProductOrderController;

Route::prefix('products')->group(function(){
    Route::post('productListing',[DistributorProductOrderController::class,'productListing'])->name('products.productListing');
    Route::post('addToCart',[DistributorProductOrderController::class,'addToCart'])->name('products.addToCart');
    Route::post('cartProduct',[DistributorProductOrderController::class,'cartProduct'])->name('products.cartProduct');
    Route::post('removeCartProduct',[DistributorProductOrderController::class,'removeCartProduct'])->name('products.removeCartProduct');
});


