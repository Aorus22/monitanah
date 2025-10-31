<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorHistory extends Model
{
    protected $table = 'sensor_histories'; // Assuming table name matches migration
    protected $fillable = ['sensor_id', 'ph', 'suhu', 'tds', 'status_pump_ph', 'status_pump_ppm'];

    protected $casts = [
        'ph' => 'float',
        'suhu' => 'float',
        'tds' => 'float',
        'status_pump_ph' => 'boolean',
        'status_pump_ppm' => 'boolean',
    ];

}
