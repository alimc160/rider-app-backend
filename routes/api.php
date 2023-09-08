<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\Rider\BookingOrderController;
use App\Http\Controllers\Rider\RiderController;
use App\Http\Controllers\Rider\RiderVehicleController;
use App\Http\Controllers\VehicleTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Rider\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('rider')->group(function (){
    Route::post('/register', [ AuthController::class, 'register']);
    Route::post('/generate-token', [ AuthController::class, 'generateToken']);
    Route::post('/resend-otp',[AuthController::class,'resendOtp']);
    Route::post('/login',[AuthController::class,'login']);
    Route::middleware(['auth:sanctum','role:rider'])->group(function (){
        Route::get('/get-profile',[AuthController::class,'getProfile']);
        Route::post('/update-profile',[AuthController::class,'updateProfile']);
        Route::post('/add-vehicle-details',[RiderVehicleController::class,'addVehicleDetails']);
        Route::post('/update-active-status',[RiderController::class,'updateOnlineStatus']);
        Route::post('logout',[AuthController::class,'logout']);
        Route::post('/add-booking-order-package',[BookingOrderController::class,'addBookingOrderPackage']);
        Route::post('/accept-booking',[BookingOrderController::class,'acceptBooking']);
        Route::post('/arrived-for-pickup-booking',[BookingOrderController::class,'arrivedForPickupBooking']);
        Route::post('/package-received-booking',[BookingOrderController::class,'packageReceivedBookingOperation']);
        Route::post('/arrived-for-dropoff-booking',[BookingOrderController::class,'arrivedForDropoffBookingOperation']);
        Route::post('/package-delivered-booking',[BookingOrderController::class,'packageDeliveredBookingOperation']);
        Route::get('/get-orders-listing',[BookingOrderController::class,'getPastOrdersListing']);
        Route::get('/orders/{id}',[BookingOrderController::class,'getBookingOrderDetails']);
    });
});

Route::post('/add-booking-order',[BookingOrderController::class,'addBookingOrder']);
Route::middleware('auth:sanctum')->group(function (){
    Route::prefix('general')->group(function (){
        Route::get('cities',[CityController::class,'citiesList']);
        Route::get('/vehicle-types',[VehicleTypeController::class,'getList']);
    });
});
