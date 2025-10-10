<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorData;
use App\Models\PpmPumpLog;


class MonitoringController extends Controller
{
    public function index()
    {
        $logs = \App\Models\SensorHistory::where('status_pump_ph', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $logsppm = \App\Models\SensorHistory::where('status_pump_ppm', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'ppm');

        $phLogs = $logs;
        $ppmLogs = $logsppm;

        // Ambil updated_at terbaru
        $lastUpdated = SensorData::latest('updated_at')->value('updated_at');

        return view('monitoring.index', compact('logs','logsppm','phLogs','ppmLogs','lastUpdated'));
    }

    public function getSensorHistory(Request $request)
    {
        $sensorId = $request->input('sensor_id', 1); // Default sensor 1

        // Karena di DB belum ada field sensor_id, return kosong untuk sensor 2,3,4
        if ($sensorId != 1) {
            return response()->json([]);
        }

        $data = SensorData::latest()->limit(50)->get()->reverse()->values();
        return response()->json($data);
    }

    //update
public function selectPlant(Request $request)
{
    $request->validate([
        'plant_id' => 'required|exists:plant_fish_settings,id',
    ]);

    session(['selected_plant_id' => $request->plant_id]);

    return redirect()->back()->with('success', 'Tanaman berhasil dipilih.');
}



}
