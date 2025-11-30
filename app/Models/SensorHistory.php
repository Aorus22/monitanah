<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorHistory extends Model
{
    protected $table = 'sensor_histories'; // Assuming table name matches migration
    protected $fillable = [
        'parameter',
        'sensor_no',
        'value',
        'status_pump_ph',
        'status_pump_ppm',
    ];

    protected $casts = [
        'value' => 'float',
        'status_pump_ph' => 'boolean',
        'status_pump_ppm' => 'boolean',
    ];

}
