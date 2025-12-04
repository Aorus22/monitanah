<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorData;
use App\Models\PpmPumpLog;


class MonitoringController extends Controller
{
    public function index()
    {
        $logs = \App\Models\SensorHistory::where('parameter', 'ph')
            ->where('status_pump_ph', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $logsppm = \App\Models\SensorHistory::where('parameter', 'tds')
            ->where('status_pump_ppm', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'ppm');

        $phLogs = $logs;
        $ppmLogs = $logsppm;

        // Ambil updated_at terbaru
        $lastUpdated = SensorData::latest('updated_at')->value('updated_at');

        return view('monitoring.index', compact('logs','logsppm','phLogs','ppmLogs','lastUpdated'));
    }

    // public function getSensorHistory(Request $request)
    // {
    //     $parameter = $request->input('parameter', 'ph'); // Default parameter pH
    //     $sensorNo = $request->input('sensor_no', 1); // Default sensor 1

    //     $data = SensorData::where('parameter', $parameter)
    //         ->where('sensor_no', $sensorNo)
    //         ->latest()
    //         ->limit(50)
    //         ->get()
    //         ->reverse()
    //         ->values();
    //     return response()->json($data);
    // }

    public function getSensorHistory(Request $request)
    {
        $parameter = $request->input('parameter', 'ph'); // default pH
        $sensorNo = $request->input('sensor_no', 1);     // default sensor 1
    
        // Group by 1 hour
        $data = SensorData::selectRaw('
                DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour,
                AVG(value) as avg_value
            ')
            ->where('parameter', $parameter)
            ->where('sensor_no', $sensorNo)
            ->groupBy('hour')
            ->orderBy('hour', 'DESC')
            ->limit(720)  // ambil 1 Bulan terakhir
            ->get()
            ->reverse() // biar urutan dari lama ke baru
            ->values();
    
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
