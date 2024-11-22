<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController\AdminBillController;
use App\Http\Controllers\Api\AdminController\AdminOrderController;
use App\Http\Controllers\Api\AdminController\AdminLaserController;
use App\Http\Controllers\Api\AdminController\AdminCredController;
use App\Http\Controllers\Api\AdminController\AdminDashboardController;
use App\Http\Controllers\Api\AdminController\AdminReportController;
use App\Http\Controllers\Api\AdminController\AdminDistributorController;    
use App\Http\Controllers\Api\AdminController\AdminDriverController;
use App\Http\Controllers\AdminController\AdminProductController;
use App\Http\Controllers\Api\AdminController\AdminMapProductController;
use App\Http\Controllers\Api\AdminController\AdminPaymentController;
use App\Http\Controllers\Api\AdminController\AdminOrderManagmentController;

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

Route::prefix('adminDistributor')->group(function(){
    Route::post('distributorListing',[AdminDistributorController::class,'distributorListing'])->name('adminDistributor.distributorListing');
    Route::post('distributorDetailListing',[AdminDistributorController::class,'distributorDetailListing'])->name('adminDistributor.distributorDetailListing');
    Route::post('addDistributor',[AdminDistributorController::class,'addDistributor'])->name('adminDistributor.addDistributor');
    Route::post('updateDistributor',[AdminDistributorController::class,'updateDistributor'])->name('adminDistributor.updateDistributor');
    Route::post('deleteOrRestoreDistributor',[AdminDistributorController::class,'deleteOrRestoreDistributor'])->name('adminDistributor.deleteOrRestoreDistributor');
});

Route::prefix('adminDriver')->group(function(){
    Route::post('driverListing',[AdminDriverController::class,'driverListing'])->name('adminDriver.driverListing');
    Route::post('driverDetailListing',[AdminDriverController::class,'driverDetailListing'])->name('adminDriver.driverDetailListing');
    Route::post('addDriver',[AdminDriverController::class,'addDriver'])->name('adminDriver.addDriver');
    Route::post('updateDriver',[AdminDriverController::class,'updateDriver'])->name('adminDriver.updateDriver');
    Route::post('deleteOrRestoreDriver',[AdminDriverController::class,'deleteOrRestoreDriver'])->name('adminDriver.deleteOrRestoreDriver');
});

Route::prefix('adminProduct')->group(function(){
    Route::post('productListing',[AdminProductController::class,'productListing'])->name('adminProduct.productListing');
    Route::post('addProduct',[AdminProductController::class,'addProduct'])->name('adminProduct.addProduct');
    Route::post('updateProduct',[AdminProductController::class,'updateProduct'])->name('adminProduct.updateProduct');
    Route::post('deleteOrRestoreProduct',[AdminProductController::class,'deleteOrRestoreProduct'])->name('adminProduct.deleteOrRestoreProduct');
});

Route::prefix('mapProductPrice')->group(function(){
    Route::post('mapProductPriceListing',[AdminMapProductController::class,'mapProductPriceListing'])->name('mapProductPrice.mapProductPriceListing');
    Route::post('distrubutorListing',[AdminMapProductController::class,'distrubutorListing'])->name('mapProductPrice.distrubutorListing');
    Route::post('productListing',[AdminMapProductController::class,'productListing'])->name('mapProductPrice.productListing');
    Route::post('addMapProductPrice',[AdminMapProductController::class,'addMapProductPrice'])->name('mapProductPrice.addMapProductPrice');
    Route::post('updateProductPrice',[AdminMapProductController::class,'updateProductPrice'])->name('mapProductPrice.updateProductPrice');
});


Route::prefix('adminPayment')->group(function(){
    Route::post('distributorListing',[AdminPaymentController::class,'distributorListing'])->name('adminPayment.distributorListing');
    Route::post('paymentListing',[AdminPaymentController::class,'paymentListing'])->name('adminPayment.paymentListing');
    Route::post('addPayment',[AdminPaymentController::class,'addPayment'])->name('adminPayment.addPayment');
});

Route::prefix('adminOrderManagment')->group(function(){
    Route::post('orderManagmentListing',[AdminOrderManagmentController::class,'orderManagmentListing'])->name('adminOrderManagment.orderManagmentListing');
    Route::post('orderManagmentDetails',[AdminOrderManagmentController::class,'orderManagmentDetails'])->name('adminOrderManagment.orderManagmentDetails');
    Route::post('updateOrderStatus',[AdminOrderManagmentController::class,'updateOrderStatus'])->name('adminOrderManagment.updateOrderStatus');
    Route::post('updateOrderProducts',[AdminOrderManagmentController::class,'updateOrderProducts'])->name('adminOrderManagment.updateOrderProducts');
    Route::post('productListing',[AdminOrderManagmentController::class,'productListing'])->name('adminOrderManagment.productListing');
    Route::post('distributorListing',[AdminOrderManagmentController::class,'distributorListing'])->name('adminOrderManagment.distributorListing');
    Route::post('productListingForDistributor',[AdminOrderManagmentController::class,'productListingForDistributor'])->name('adminOrderManagment.productListingForDistributor');
    Route::post('addToCart',[AdminOrderManagmentController::class,'addToCart'])->name('adminOrderManagment.addToCart');
    Route::post('cartListing',[AdminOrderManagmentController::class,'cartListing'])->name('adminOrderManagment.cartListing');
    Route::post('updateCartProductQuantity',[AdminOrderManagmentController::class,'updateCartProductQuantity'])->name('adminOrderManagment.updateCartProductQuantity');
    Route::post('removeCartProduct',[AdminOrderManagmentController::class,'removeCartProduct'])->name('adminOrderManagment.removeCartProduct');
    Route::post('orderPlaced',[AdminOrderManagmentController::class,'orderPlaced'])->name('adminOrderManagment.orderPlaced');
});
