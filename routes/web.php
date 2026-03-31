<?php

use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\VehicleTypeController;
use App\Http\Controllers\OrderTrackingController;
use Illuminate\Support\Facades\Route;
use App\Models\VehicleType;

Route::get('/', function () {

    $vehicles = VehicleType::where('is_active', true)->get();

    // 2. Віддаємо їх у вигляд 'welcome'
    return view('welcome', compact('vehicles'));
});

Route::get('/order/track/{token}', [OrderTrackingController::class, 'show']);
