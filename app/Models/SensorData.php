<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    protected $table = 'sensor_data';
    protected $fillable = ['sensor_id', 'ph', 'suhu', 'tds', 'status_pump_ph', 'status_pump_ppm','updated_at'];

    protected $casts = [
        'ph' => 'float',
        'suhu' => 'float',
        'tds' => 'float',
        'status_pump_ph' => 'boolean',
        'status_pump_ppm' => 'boolean',
    ];
}

