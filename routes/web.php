<?php

use Illuminate\Support\Facades\Route;
use App\Models\VehicleType;

Route::get('/', function () {

    $vehicles = VehicleType::where('is_active', true)->get();
    
    // 2. Віддаємо їх у вигляд 'welcome'
    return view('welcome', compact('vehicles'));
});
