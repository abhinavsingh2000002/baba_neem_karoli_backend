<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\DistrubutorMiddleware;
use App\Http\Controllers\DistributorController\ShopProductController;
use App\Http\Controllers\DistributorController\CartController;
use App\Http\Controllers\DistributorController\OrderPlacedController;



Route::middleware(DistrubutorMiddleware::class)->group(function(){

    // Route for Product Order By the distributors Start---------------------------------------
    Route::prefix('distributor_product')->group(function(){
        Route::get('index',[ShopProductController::class,'index'])->name('distributor_product.index');
        Route::post('listing',[ShopProductController::class,'listing'])->name('distributor_product.listing');
        Route::get('productDetail/{id}',[ShopProductController::class,'productDetails'])->name('distributor_product.productDetail');
    });
    // Route for Product Order By the distributors End---------------------------------------


     // Route for Product Order By the distributors Start---------------------------------------
     Route::prefix('cart')->group(function(){
        Route::get('index',[CartController::class,'index'])->name('cart.index');
        Route::post('add_to_cart',[CartController::class,'addToCart'])->name('cart.add_to_cart');
        Route::post('listing',[CartController::class,'listing',])->name('cart.listing');
        Route::get('delete',[CartController::class,'delete',])->name('cart.delete');
    });
    // Route for Product Order By the distributors End---------------------------------------


     // Route for Order Placed  By the distributors Start---------------------------------------
     Route::prefix('order')->group(function(){
        Route::get('orderPlaced',[OrderPlacedController::class,'oderPlaced'])->name('order.orderPlaced');
        // Route::post('add_to_cart',[OrderPlacedController::class,'addToCart'])->name('cart.add_to_cart');
        // Route::post('listing',[OrderPlacedController::class,'listing',])->name('cart.listing');
        // Route::get('delete',[OrderPlacedController::class,'delete',])->name('cart.delete');
    });
    // Route for  Order Placed By the distributors End---------------------------------------
});
