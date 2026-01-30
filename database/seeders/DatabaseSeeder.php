<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Dispatch;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin Account
        Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@dispatch.com',
            'password' => Hash::make('admin123'),
        ]);

        // Create Sample Vehicles
        $vehicles = [];
        $vehicles[] = Vehicle::create([
            'vehicle_number' => 'TRK-001',
            'vehicle_type' => 'Delivery Van',
            'driver_name' => 'Mark Kian De Quiros',
            'driver_phone' => '09171234567',
            'status' => 'available',
        ]);

        $vehicles[] = Vehicle::create([
            'vehicle_number' => 'TRK-002',
            'vehicle_type' => 'Cargo Truck',
            'driver_name' => 'Christian Ventura',
            'driver_phone' => '09187654321',
            'status' => 'available',
        ]);

        $vehicles[] = Vehicle::create([
            'vehicle_number' => 'TRK-003',
            'vehicle_type' => 'Mini Van',
            'driver_name' => 'Angelo Gutoman',
            'driver_phone' => '09191234567',
            'status' => 'available',
        ]);

         $vehicles[] = Vehicle::create([
            'vehicle_number' => 'TRK-004',
            'vehicle_type' => 'Kulong-Kulong Truck',
            'driver_name' => 'Noel Mara',
            'driver_phone' => '09143534542',
            'status' => 'available',
        ]);

        // Create sample verified users
        User::create([
        'name' => 'User 1',
        'email' => 'user1@example.com',
        'phone' => '09190000000',
        'password' => Hash::make('password123'),
        'is_verified' => true,
        'email_verified_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
        ]);

        echo "✅ Admin created: admin@dispatch.com / admin123\n";
        echo "✅ User created: user1@example.com / password123\n";
        echo "✅ 3 Vehicles created\n";
    }
}