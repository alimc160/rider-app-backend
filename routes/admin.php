<?php

use App\Http\Controllers\Admin\VehicleCategoryController;
use App\Http\Controllers\VehicleTypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\RiderController;
use App\Http\Controllers\Admin\VehicleCompanyController;

Route::post('/login',[AuthController::class,'login']);
Route::middleware(['auth:sanctum','role:admin'])->group(function (){
    Route::resource('/vehicle-types',VehicleTypeController::class);
    Route::resource('vehicle-category',VehicleCategoryController::class);
    Route::post('logout',[AuthController::class,'logout']);
    Route::resource('vehicle_companies',VehicleCompanyController::class);
    Route::prefix('general')->group(function (){
        Route::get('vehicle-categories-list',[VehicleCategoryController::class,'getCategoriesList']);
        Route::get('vehicle-companies-list',[VehicleCompanyController::class,'getCompaniesList']);
    });
    Route::prefix('rider')->group(function () {
        Route::get('/list',[RiderController::class,'getList']);
        Route::get('/rider-details',[RiderController::class,'getRiderDetails']);
        Route::post('/update-status',[RiderController::class,'updateStatus']);
        Route::post('/update-file-status',[RiderController::class,'updateFileStatus']);
        Route::post('/update-rider-vehicle-status',[RiderController::class,'updateRiderVehicleStatus']);
    });
});
