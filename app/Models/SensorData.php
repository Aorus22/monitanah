<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    protected $table = 'sensor_data';
    protected $fillable = [
        'parameter',
        'sensor_no',
        'value',
        'status_pump_ph',
        'status_pump_ppm',
        'updated_at',
    ];

    protected $casts = [
        'value' => 'float',
        'status_pump_ph' => 'boolean',
        'status_pump_ppm' => 'boolean',
    ];
}
