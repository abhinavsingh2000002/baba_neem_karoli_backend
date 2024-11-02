<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DriverController\DriverAllotedTaskController;

Route::prefix('allotedTask')->group(function(){
    Route::post('allotedTaskListing',[DriverAllotedTaskController::class,'allotedTaskListing'])->name('allotedTask.allotedTaskListing');
});
