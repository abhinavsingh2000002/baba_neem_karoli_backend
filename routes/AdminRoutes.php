<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController\DistributorController;
use App\Http\Controllers\AdminController\DriverController;
use App\Http\Controllers\AdminController\ProductController;
use App\Http\Controllers\AdminController\MapProductController;
use App\Http\Controllers\AdminController\AdminOrderController;
use App\Http\Controllers\AdminController\AdminBillController;
use App\Http\Controllers\AdminController\AdminDriverTaskController;
use App\Http\Controllers\AdminController\AdminCredController;
use App\Http\Controllers\AdminController\AdminLedgerController;
use App\Http\Controllers\AdminController\AdminOrderReportController;



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
    Route::prefix('admin_order')->group(function(){
        Route::get('index',[AdminOrderController::class,'index'])->name('admin_order.index');
        Route::get('listing',[AdminOrderController::class,'listing'])->name('admin_order.listing');
        Route::get('detailListing/{id}',[AdminOrderController::class,'detailListing'])->name('admin_order.detailListing');
        Route::get('filter',[AdminOrderController::class,'listing'])->name('admin_order.filter');
        Route::get('edit/{id}',[AdminOrderController::class,'edit'])->name('admin_order.edit');
        Route::put('update/{id}',[AdminOrderController::class,'update'])->name('admin_order.update');
        Route::post('updateStatus',[AdminOrderController::class,'updateStatus'])->name('admin_order.updateStatus');
        Route::get('add',[AdminOrderController::class,'add'])->name('admin_order.add');
        Route::post('product_listing',[AdminOrderController::class,'productListing'])->name('admin_order.product_listing');
        Route::get('productDetails/{id}/{user_id}',[AdminOrderController::class,'productDetails'])->name('admin_order.productDetails');
        Route::post('add_to_cart',[AdminOrderController::class,'add_to_cart'])->name('admin_order.add_to_cart');
        Route::get('cart_index',[AdminOrderController::class,'cart_index'])->name('admin_order.cart_index');
        Route::post('cart_listing',[AdminOrderController::class,'cart_listing'])->name('admin_order.cart_listing');
        Route::get('cart_delete',[AdminOrderController::class,'cart_delete'])->name('admin_order.cart_delete');
        Route::get('oderPlaced',[AdminOrderController::class,'oderPlaced'])->name('admin_order.oderPlaced');
    });
    // Order Routes End----------------------------------------------------------------------------------------


    // Bills Routes Start--------------------------------------------------------------------------------------
     Route::prefix('admin_bills')->group(function(){
        Route::get('index',[AdminBillController::class,'index'])->name('admin_bills.index');
        Route::get('listing',[AdminBillController::class,'listing'])->name('admin_bills.listing');
        Route::get('billDetail/{id}',[AdminBillController::class,'billDetail'])->name('admin_bills.billDetail');
        Route::get('invoicePdf/{id}',[AdminBillController::class,'invoicePdf'])->name('admin_bills.invoicePdf');
    });
    // Bills Routes End----------------------------------------------------------------------------------------

     // Driver Task Routes Start--------------------------------------------------------------------------------------
     Route::prefix('admin_driver_task')->group(function(){
        Route::get('index',[AdminDriverTaskController::class,'index'])->name('admin_driver_task.index');
        Route::get('listing',[AdminDriverTaskController::class,'listing'])->name('admin_driver_task.listing');
        Route::get('add',[AdminDriverTaskController::class,'add'])->name('admin_driver_task.add');
        Route::post('create',[AdminDriverTaskController::class,'add'])->name('admin_driver_task.create');
        Route::get('edit/{id}',[AdminDriverTaskController::class,'edit'])->name('admin_driver_task.edit');
        Route::post('update{id}',[AdminDriverTaskController::class,'update'])->name('admin_driver_task.update');
    });
    // Driver Task Routes End----------------------------------------------------------------------------------------


     // Driver Task Routes Start--------------------------------------------------------------------------------------
     Route::prefix('admin_cred')->group(function(){
        Route::get('index',[AdminCredController::class,'index'])->name('admin_cred.index');
        Route::get('listing',[AdminCredController::class,'listing'])->name('admin_cred.listing');
    });
    // Driver Task Routes End----------------------------------------------------------------------------------------

    // Ledger Routes Start--------------------------------------------------------------------------
    Route::prefix('admin_ledger')->group(function(){
        Route::get('index',[AdminLedgerController::class,'index'])->name('admin_ledger.index');
        Route::get('listing',[AdminLedgerController::class,'listing'])->name('admin_ledger.listing');
        Route::get('ledgerpdf',[AdminLedgerController::class,'ledgerpdf'])->name('admin_ledger.ledgerpdf');
        Route::get('ledgerexcel',[AdminLedgerController::class,'ledgerexcel'])->name('admin_ledger.ledgerexcel');
    });
    // Ledger Routes End--------------------------------------------------------------------------


    // Order Report Routes Start--------------------------------------------------------------------------
    Route::prefix('admin_order_report')->group(function(){
        Route::get('index',[AdminOrderReportController::class,'index'])->name('admin_order_report.index');
        Route::get('listing',[AdminOrderReportController::class,'listing'])->name('admin_order_report.listing');
        Route::get('reportpdf',[AdminOrderReportController::class,'reportpdf'])->name('admin_order_report.reportpdf');
        Route::get('reportExcel',[AdminOrderReportController::class,'reportExcel'])->name('admin_order_report.reportExcel');
    });
    // Order Report Routes End--------------------------------------------------------------------------


});
