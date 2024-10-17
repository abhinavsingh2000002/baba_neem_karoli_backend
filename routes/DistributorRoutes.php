<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\DistrubutorMiddleware;
use App\Http\Controllers\DistributorController\ShopProductController;
use App\Http\Controllers\DistributorController\CartController;
use App\Http\Controllers\DistributorController\OrderPlacedController;
use App\Http\Controllers\DistributorController\DistributorBillController;
use App\Http\Controllers\DistributorController\DistributorCredController;



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
        Route::GET('index',[OrderPlacedController::class,'index'])->name('order.index');
        Route::GET('orderPlaced',[OrderPlacedController::class,'oderPlaced'])->name('order.orderPlaced');
        Route::POST('listing',[OrderPlacedController::class,'listing',])->name('order.listing');
        Route::GET('listingDetail/{id}',[OrderPlacedController::class,'listingDetail',])->name('order.listingDetail');
        Route::GET('invoicePdf/{id}',[OrderPlacedController::class,'invoicePdf',])->name('order.invoicePdf');
    });
    // Route for  Order Placed By the distributors End---------------------------------------

    // Route for Order Placed  By the distributors Start---------------------------------------
      Route::prefix('bills')->group(function(){
        Route::get('index',[DistributorBillController::class,'index'])->name('bills.index');
       Route::post('listing',[DistributorBillController::class,'listing'])->name('bills.listing');
       Route::get('billDetail/{id}',[DistributorBillController::class,'billDetail',])->name('bills.billDetail');
       Route::get('invoicePdf/{id}',[DistributorBillController::class,'invoicePdf',])->name('bills.invoicePdf');
   });
   // Route for  Order Placed By the distributors End---------------------------------------

    // Route for Distribor Cred Start------------------------------------------------------
        Route::prefix('distributor_cred')->group(function(){
            Route::get('index',[DistributorCredController::class,'index'])->name('distributor_cred.index');
            Route::get('listing',[DistributorCredController::class,'listing'])->name('distributor_cred.listing');
        });
    // Route for Distribor Cred End------------------------------------------------------
});
