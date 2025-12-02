<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorData;
use App\Models\SensorHistory;
use App\Models\PhPumpLog;
use App\Models\PpmPumpLog;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;


class AquaponicController extends Controller
{
    /**
     * ✅ Endpoint untuk menerima data realtime setiap 5 detik dari ESP32
     * Route: POST /api/sensor/realtime
     * Data ini tidak disimpan ke database, hanya untuk monitoring sementara
     */
    public function updateRealtime(Request $request)
    {
        $request->validate([
            'parameter' => 'required|string|in:ph,suhu,tds',
            'sensor_no' => 'required|integer|min:1',
            'value' => 'required|numeric',
            'status_pump_ph' => 'sometimes|boolean',
            'status_pump_ppm' => 'sometimes|boolean',
        ]);

        $parameter = strtolower($request->parameter);
        $sensorNo = $request->sensor_no;

        $payload = [
            'value' => $request->value,
            'status_pump_ph' => $request->boolean('status_pump_ph'),
            'status_pump_ppm' => $request->boolean('status_pump_ppm'),
        ];

        SensorData::updateOrCreate(
            [
                'parameter' => $parameter,
                'sensor_no' => $sensorNo,
            ],
            $payload
        );

        return response()->json([
            'message' => 'Realtime data updated',
            'parameter' => $parameter,
            'sensor_no' => $sensorNo,
        ], 200);
    }
    /**
     * ✅ Endpoint untuk menyimpan data history tiap 5 menit dari ESP32
     * Route: POST /api/sensor/history
     * Data disimpan ke tabel sensor_data
     */
    public function storeHistory(Request $request)
    {

        $request->validate([
            'parameter' => 'required|string|in:ph,suhu,tds',
            'sensor_no' => 'required|integer|min:1',
            'value' => 'required|numeric',
            'status_pump_ph' => 'sometimes|boolean',
            'status_pump_ppm' => 'sometimes|boolean',
        ]);

        $history = SensorHistory::create([
            'parameter' => strtolower($request->parameter),
            'sensor_no' => $request->sensor_no,
            'value' => $request->value,
            'status_pump_ph' => $request->boolean('status_pump_ph'),
            'status_pump_ppm' => $request->boolean('status_pump_ppm'),
        ]);

        return response()->json([
            'message' => 'Sensor history saved',
            'data' => $history,
        ], 200);
    }
    public function getHistory()
    {
        $sensorNo = request()->input('sensor_no', 1);
        $parameter = request()->input('parameter', 'ph');

        $history = SensorHistory::where('parameter', $parameter)
            ->where('sensor_no', $sensorNo)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get(['parameter', 'sensor_no', 'value', 'created_at']);

        return response()->json($history->reverse()->values()); // reverse biar urut dari lama ke baru
    }

    // Mengecek pH dan trigger pompa jika kurang dari 6.3 dan belum 4x
    public function checkPhAndTriggerPump()
    {
    $latest = SensorData::where('parameter', 'ph')
        ->where('sensor_no', 1)
        ->first(); // Data realtime terbaru for sensor 1

        if (!$latest) {
            return response()->json(['status' => 'no_data'], 404);
        }

        $todaySprayCount = PhPumpLog::whereDate('sprayed_at', now())->count();
        $minPh = 6.3;

        // Cek apakah pH kurang dari 6.3 dan belum mencapai limit 4x per hari
        if ($latest->value < $minPh && $todaySprayCount < 4) {
            // Catat log sebelum semprot
            PhPumpLog::create([
                'ph_before' => $latest->value,
                'spray_number' => $todaySprayCount + 1,
                'sprayed_at' => now(),
            ]);

            return response()->json([
                'status' => 'pump_triggered',
                'ph_before' => $latest->value,
                'spray_count' => $todaySprayCount + 1
            ]);
        }

        return response()->json([
            'status' => 'no_action_needed',
            'ph_current' => $latest->value,
            'spray_count_today' => $todaySprayCount
        ]);
    }


    // Status pompa apakah boleh aktif atau masih delay 15 menit untuk ESP
    public function pumpStatus()
    {
    $latest = SensorData::where('parameter', 'ph')
        ->where('sensor_no', 1)
        ->first();
        $latestLog = PhPumpLog::latest()->first();

        $minPh = 6.3;

        if (!$latest || !$latest->value) {
            return response('off', 200, ['Content-Type' => 'text/plain']);
        }

        if ($latest->value >= $minPh) {
            return response('off', 200, ['Content-Type' => 'text/plain']);
        }

        // Batas 4x per hari
        $todaySprayCount = PhPumpLog::whereDate('sprayed_at', now())->count();
        if ($todaySprayCount >= 4) {
            return response('off', 200, ['Content-Type' => 'text/plain']);
        }

        return response('on', 200, ['Content-Type' => 'text/plain']);
    }

    // Update nilai pH setelah semprotan dilakukan oleh ESP32
    public function updateAfterPh(Request $request)
    {
        $request->validate([
            'ph_after' => 'required|numeric',
        ]);

        $latest = PhPumpLog::latest()->first();

        if ($latest) {
            $latest->update(['ph_after' => $request->ph_after]);
        }

        return response()->json(['success' => true]);
    }
    // Cek apakah perlu menyemprot ppm
    public function checkPpmAndTriggerPump()
    {
    $latest = SensorData::where('parameter', 'tds')
        ->where('sensor_no', 1)
        ->first(); // Data realtime terbaru for sensor 1

        if (!$latest) {
            return response()->json(['status' => 'no_data'], 404);
        }

        $todaySprayCount = PpmPumpLog::whereDate('sprayed_at', now())->count();
        $minPpm = 600; // Batas aman minimal PPM (TDS)

        // Cek apakah TDS kurang dari 300 dan belum mencapai limit 4x per hari
        if ($latest->value < $minPpm && $todaySprayCount < 4) {
            // Catat log sebelum semprot
            PpmPumpLog::create([
                'ppm_before' => $latest->value,
                'spray_number' => $todaySprayCount + 1,
                'sprayed_at' => now(),
            ]);

            return response()->json([
                'status' => 'pump_triggered',
                'ppm_before' => $latest->value,
                'spray_count' => $todaySprayCount + 1
            ]);
        }

        return response()->json([
            'status' => 'no_action_needed',
            'ppm_current' => $latest->value,
            'spray_count_today' => $todaySprayCount
        ]);
    }

    public function pumpStatusPpm()
    {
    $latest = SensorData::where('parameter', 'tds')
        ->where('sensor_no', 1)
        ->first();
        $latestLog = PpmPumpLog::latest()->first();

        if (!$latest || !$latest->value) {
            return response('off', 200, ['Content-Type' => 'text/plain']);
        }

        $minPpm = 600;

        if ($latest->value >= $minPpm) {
            return response('off', 200, ['Content-Type' => 'text/plain']);
        }

        // Cek apakah log hari ini sudah menyemprot lebih dari 4x
        $todaySprayCount = PpmPumpLog::whereDate('sprayed_at', now())->count();
        if ($todaySprayCount >= 4) {
            return response('off', 200, ['Content-Type' => 'text/plain']);
        }

        return response('on', 200, ['Content-Type' => 'text/plain']);
    }



    // Diupdate oleh ESP32 setelah delay
    public function updateAfterPpm(Request $request)
    {
        $request->validate([
            'ppm_after' => 'required|numeric',
        ]);

        $latest = PpmPumpLog::latest()->first();

        if ($latest) {
            $latest->update(['ppm_after' => $request->ppm_after]);
        }

        return response()->json(['success' => true]);
    }

    // Tampilkan history log ppm
    public function showHistory()
    {
        $logs = PpmPumpLog::orderBy('sprayed_at', 'desc')->paginate(10);
        return view('ppm_pump_history', compact('logs'));
    }
}
