<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pickup_location',
        'delivery_location',
        'dispatch_date',
        'dispatch_time',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'dispatch_date' => 'date',
            'dispatch_time' => 'datetime:H:i',
        ];
    }

    // Many-to-One: Dispatch belongs to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Many-to-Many: Dispatch belongs to many Vehicles (through pivot)
    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'dispatch_vehicle')
                    ->withPivot('assigned_at')
                    ->withTimestamps();
    }
}