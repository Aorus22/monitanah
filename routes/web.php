<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\AquaponicController;
use App\Http\Controllers\SensorExportController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman publik
Route::get('/', [MonitoringController::class, 'index'])->name('home');
// Route::get('/', fn() => view('welcome'))->name('home');
// Route::view('/about', 'about')->name('about');
// Route::view('/contact', 'contact')->name('contact');

// Proses Contact Form
// Route::post('/contact', function (\Illuminate\Http\Request $request) {
//     $validated = $request->validate([
//         'name'    => 'required|string|max:255',
//         'email'   => 'required|email|max:255',
//         'subject' => 'required|string|max:255',
//         'message' => 'required|string',
//     ]);
//     // TODO: Kirim email / simpan db
//     return back()
//         ->with('success', 'Terima kasih! Pesan Anda telah terkirim.')
//         ->withInput();
// })->name('contact.submit');

// Dashboard & API Monitoring
// Route::prefix('monitoring')->group(function () {
//     // Tampilan dashboard
//     Route::get('/', [MonitoringController::class, 'index'])
//         ->name('monitoring.index');

// });
//Tampilan Pompa
// Route::get('/ph-pump-history', [AquaponicController::class, 'showHistory']);

// Endpoint untuk menerima data ESP32
Route::post('/esp/sensor-data', [MonitoringController::class, 'receiveEspData'])
    ->name('esp.receive_data');

// Export Routes - Direct download, no form needed
Route::prefix('export')->name('sensor.export.')->group(function () {
    // Route::get('/', [SensorExportController::class, 'showExportForm'])->name('form'); // Not needed anymore
    Route::get('/pdf', [SensorExportController::class, 'exportPdf'])->name('pdf');
    Route::get('/excel', [SensorExportController::class, 'exportExcel'])->name('excel');
});

// Jika Anda perlu proxy API tanpa auth:
// Route::get('/api/sensor/{type}', [MonitoringController::class, 'getLiveData'])
//     ->where('type', 'ph|suhu|tds')
//     ->name('api.sensor');

// Jika Anda menggunakan Auth scaffolding:
// Auth::routes();
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
