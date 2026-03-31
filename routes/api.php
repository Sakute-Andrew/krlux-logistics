<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\VehicleTypeController;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/', function () {

    $vehicles = VehicleType::where('is_active', true)->get();

    // 2. Віддаємо їх у вигляд 'welcome'
    return view('welcome', compact('vehicles'));
});


Route::get('/vehicle-types', [VehicleTypeController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/route', function (Request $request) {
    $from = $request->query('from'); // "lng,lat"
    $to   = $request->query('to');   // "lng,lat"

    $url = "https://router.project-osrm.org/route/v1/driving/{$from};{$to}?overview=full&geometries=geojson";

    $response = \Illuminate\Support\Facades\Http::get($url);

    return response()->json($response->json());
});
