<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['vehicle_number', 'vehicle_type', 'driver_name', 'driver_phone', 'status'];
}
