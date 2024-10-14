<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController\DistributorController;
use App\Http\Controllers\AdminController\DriverController;
use App\Http\Controllers\AdminController\ProductController;
use App\Http\Controllers\AdminController\MapProductController;
use App\Http\Controllers\AdminController\OrderController;

Route::middleware(AdminMiddleware::class)->group(function(){
    // distributor Routes start--------------------------------------------------------------------------
    Route::prefix('distributor')->group(function(){
        Route::get('index',[DistributorController::class,'index'])->name('distributor.index');
        Route::get('listing',[DistributorController::class,'listing'])->name('distributor.listing');
        Route::get('add',[DistributorController::class,'add'])->name('distributor.add');
        Route::post('create',[DistributorController::class,'add'])->name('distributor.create');
        Route::get('view/{id}',[DistributorController::class,'view'])->name('distributor.view');
        Route::get('edit/{id}',[DistributorController::class,'edit'])->name('distributor.edit');
        Route::post('update/{id}',[DistributorController::class,'update'])->name('distributor.update');
        Route::get('delete/{id}',[DistributorController::class,'delete'])->name('distributor.delete');
    });
    // distributor Routes End------------------------------------------------------------------------------


    // Driver Routes Start----------------------------------------------------------------------------------
    Route::prefix('driver')->group(function(){
        Route::get('index',[DriverController::class,'index'])->name('driver.index');
        Route::get('listing',[DriverController::class,'listing'])->name('driver.listing');
        Route::get('add',[DriverController::class,'add'])->name('driver.add');
        Route::post('create',[DriverController::class,'add'])->name('driver.create');
        Route::get('view/{id}',[DriverController::class,'view'])->name('driver.view');
        Route::get('edit/{id}',[DriverController::class,'edit'])->name('driver.edit');
        Route::post('update/{id}',[DriverController::class,'update'])->name('driver.update');
        Route::get('delete/{id}',[DriverController::class,'delete'])->name('driver.delete');
    });
     // Driver Routes End-----------------------------------------------------------------------------------


    // Product Routes Start---------------------------------------------------------------------------------
    Route::prefix('product')->group(function(){
        Route::get('index',[ProductController::class,'index'])->name('product.index');
        Route::get('listing',[ProductController::class,'listing'])->name('product.listing');
        Route::get('add',[ProductController::class,'add'])->name('product.add');
        Route::post('create',[ProductController::class,'add'])->name('product.create');
        Route::get('view/{id}',[ProductController::class,'view'])->name('product.view');
        Route::get('edit/{id}',[ProductController::class,'edit'])->name('product.edit');
        Route::post('update',[ProductController::class,'update'])->name('product.update');
        Route::get('delete/{id}',[ProductController::class,'delete'])->name('product.delete');
    });
    // Product Routes End-------------------------------------------------------------------------------------


    // Map Product Prices Routes Start------------------------------------------------------------------------
    Route::prefix('map_product_price')->group(function(){
        Route::get('index',[MapProductController::class,'index'])->name('map_product_price.index');
        Route::get('listing',[MapProductController::class,'listing'])->name('map_product_price.listing');
        Route::get('add',[MapProductController::class,'add'])->name('map_product_price.add');
        Route::post('create',[MapProductController::class,'add'])->name('map_product_price.create');
        Route::get('view/{id}',[MapProductController::class,'view'])->name('map_product_price.view');
        Route::get('edit/{id}',[MapProductController::class,'edit'])->name('map_product_price.edit');
        Route::post('update/{id}',[MapProductController::class,'update'])->name('map_product_price.update');
        Route::get('delete/{id}',[MapProductController::class,'delete'])->name('map_product_price.delete');
    });
    // Map Product Prices Routes End---------------------------------------------------------------------------


    // Order Routes Start--------------------------------------------------------------------------------------
    Route::prefix('order')->group(function(){
        Route::get('index',[OrderController::class,'index'])->name('order.index');
        Route::get('listing',[OrderController::class,'listing'])->name('order.listing');
    });
    // Order Routes End----------------------------------------------------------------------------------------
});
