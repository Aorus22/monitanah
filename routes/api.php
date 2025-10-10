<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AquaponicController;
use App\Models\SensorData;

Route::post('/sensor/realtime', [AquaponicController::class, 'updateRealtime']);
Route::post('/sensor/history', [AquaponicController::class, 'storeHistory']);
Route::get('/sensor/realtime', function (Request $request) {
    $sensorId = $request->input('sensor_id', 1); // Default sensor 1

    // Karena di DB belum ada field sensor_id, return null untuk sensor 2,3,4
    if ($sensorId != 1) {
        return response()->json(null);
    }

    $latest = SensorData::latest()->first();
    return response()->json($latest);
});
Route::get('/sensor/history', [AquaponicController::class, 'getHistory']);

Route::get('/check-ph', [AquaponicController::class, 'checkPhAndTriggerPump']);
Route::get('/pump-ph-status', [AquaponicController::class, 'pumpStatus']);
Route::post('/update-ph-after', [AquaponicController::class, 'updateAfterPh']);

Route::get('/check-ppm', [AquaponicController::class, 'checkPpmAndTriggerPump']);
Route::get('/pump-ppm-status', [AquaponicController::class, 'pumpStatusPpm']);
Route::post('/update-ppm-after', [AquaponicController::class, 'updateAfterPpm']);
Route::get('/ppm-pump-history', [AquaponicController::class, 'showHistory']);

Route::get('/pump-status', function () {
    $realtime = \App\Models\SensorData::latest()->first();
    return response()->json(['status_pump_ph' => $realtime->status_pump_ph]);
});

Route::post('/pump-reset', function () {
    \App\Models\SensorData::latest()->update(['status_pump_ph' => false]);
    return response()->json(['message' => 'Pump status reset']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
