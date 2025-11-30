<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AquaponicController;
use App\Models\SensorData;

Route::post('/sensor/history', [AquaponicController::class, 'storeHistory']);
Route::post('/sensor/realtime', [AquaponicController::class, 'updateRealtime']);
Route::get('/sensor/realtime', function (Request $request) {
    $parameter = $request->input('parameter', 'ph'); // Default parameter ph
    $sensorNo = $request->input('sensor_no', 1); // Default sensor 1

    $latest = SensorData::where('parameter', $parameter)
        ->where('sensor_no', $sensorNo)
        ->latest('updated_at')
        ->first();
    if (!$latest) {
        return response()->json(null);
    }
    return response()->json($latest);
});
Route::get('/sensor/realtime/all', function () {
    return SensorData::orderBy('parameter')->orderBy('sensor_no')->get();
});
Route::get('/sensor/history', [AquaponicController::class, 'getHistory']);
Route::get('/sensor/history/all', function () {
    $rows = \App\Models\SensorHistory::orderBy('created_at', 'desc')->get();
    $grouped = [];
    foreach ($rows as $row) {
        $key = $row->parameter . '-' . $row->sensor_no;
        if (!isset($grouped[$key])) {
            $grouped[$key] = [];
        }
        if (count($grouped[$key]) < 50) {
            $grouped[$key][] = $row;
        }
    }
    foreach ($grouped as $k => $items) {
        $grouped[$k] = array_reverse($items);
    }
    return response()->json($grouped);
});
Route::get('/sensor/available', function (Request $request) {
    $parameter = $request->input('parameter', 'ph');
    $sensors = SensorData::where('parameter', $parameter)
        ->distinct()
        ->pluck('sensor_no')
        ->values();
    return response()->json($sensors);
});

Route::get('/check-ph', [AquaponicController::class, 'checkPhAndTriggerPump']);
Route::get('/pump-ph-status', [AquaponicController::class, 'pumpStatus']);
Route::post('/update-ph-after', [AquaponicController::class, 'updateAfterPh']);

Route::get('/check-ppm', [AquaponicController::class, 'checkPpmAndTriggerPump']);
Route::get('/pump-ppm-status', [AquaponicController::class, 'pumpStatusPpm']);
Route::post('/update-ppm-after', [AquaponicController::class, 'updateAfterPpm']);
Route::get('/ppm-pump-history', [AquaponicController::class, 'showHistory']);

Route::get('/pump-status', function () {
    $realtime = \App\Models\SensorData::where('parameter', 'ph')->latest()->first();
    return response()->json(['status_pump_ph' => $realtime->status_pump_ph]);
});

Route::post('/pump-reset', function () {
    \App\Models\SensorData::where('parameter', 'ph')->latest()->update(['status_pump_ph' => false]);
    return response()->json(['message' => 'Pump status reset']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
