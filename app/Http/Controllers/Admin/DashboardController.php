<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dispatch;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalUsers = User::where('is_verified', true)->count();
        $totalDispatches = Dispatch::count();
        $pendingDispatches = Dispatch::where('status', 'pending')->count();
        $completedDispatches = Dispatch::where('status', 'completed')->count();

        // Get user registration data for chart (last 12 months)
        // Fetch users in PHP and group by month using the app timezone to avoid DB timezone mismatches
        $tz = config('app.timezone') ?? date_default_timezone_get();
        $now = Carbon::now()->setTimezone($tz);

        $usersForChart = User::where('is_verified', true)
            ->where('created_at', '>=', $now->copy()->subMonths(12))
            ->get(['created_at']);

        // Build map: ['YYYY-MM' => count]
        $registrationsMap = [];
        foreach ($usersForChart as $u) {
            $m = Carbon::parse($u->created_at)->setTimezone($tz)->format('Y-m');
            if (isset($registrationsMap[$m])) {
                $registrationsMap[$m]++;
            } else {
                $registrationsMap[$m] = 1;
            }
        }

        // Prepare chart data
        $months = [];
        $userCounts = [];

        for ($i = 11; $i >= 0; $i--) {
            $dt = $now->copy()->subMonths($i);
            $month = $dt->format('Y-m');
            $monthName = $dt->format('M Y');
            $months[] = $monthName;

            $userCounts[] = $registrationsMap[$month] ?? 0;
        }

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalDispatches',
            'pendingDispatches',
            'completedDispatches',
            'months',
            'userCounts'
        ));
    }

    public function dispatches()
    {
        $dispatches = Dispatch::with(['user', 'vehicles'])->latest()->paginate(20);
        return view('admin.dispatches', compact('dispatches'));
    }

    public function vehicles()
    {
        $vehicles = Vehicle::latest()->paginate(20);
        return view('admin.vehicles', compact('vehicles'));
    }

    public function assignVehicle(Request $request, $dispatchId)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $dispatch = Dispatch::findOrFail($dispatchId);
        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        
        $dispatch->vehicles()->attach($request->vehicle_id, ['assigned_at' => Carbon::now()]);
        $dispatch->update(['status' => 'assigned']);
        
        // Update vehicle status to in-use
        $vehicle->update(['status' => 'in-use']);

        return back()->with('success', 'Vehicle assigned successfully!');
    }

    public function updateVehicle(Request $request, $vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        $request->validate([
            'vehicle_number' => 'required|string|max:255|unique:vehicles,vehicle_number,' . $vehicleId,
            'vehicle_type' => 'required|string|max:255',
            'driver_name' => 'required|string|max:255',
            'driver_phone' => 'required|string|max:20',
            'status' => 'required|in:available,in-use,maintenance',
        ], [
            'vehicle_number.required' => 'Vehicle number is required.',
            'vehicle_number.unique' => 'This vehicle number already exists.',
            'vehicle_type.required' => 'Vehicle type is required.',
            'driver_name.required' => 'Driver name is required.',
            'driver_phone.required' => 'Driver phone is required.',
            'status.required' => 'Status is required.',
        ]);

        $vehicle->update([
            'vehicle_number' => $request->vehicle_number,
            'vehicle_type' => $request->vehicle_type,
            'driver_name' => $request->driver_name,
            'driver_phone' => $request->driver_phone,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.vehicles')->with('success', 'Vehicle updated successfully!');
    }

    public function deleteVehicle($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);

        // Detach from any dispatches
        $vehicle->dispatches()->detach();
        $vehicle->delete();

        return redirect()->route('admin.vehicles')->with('success', 'Vehicle deleted successfully!');
    }

    public function completeDispatch($dispatchId)
    {
        $dispatch = Dispatch::findOrFail($dispatchId);

        $dispatch->update(['status' => 'completed']);
        
        // Set all vehicles back to available
        foreach ($dispatch->vehicles as $vehicle) {
            $vehicle->update(['status' => 'available']);
        }

        return back()->with('success', 'Dispatch marked as completed!');
    }

    public function editDispatch(Request $request, $dispatchId)
    {
        $dispatch = Dispatch::findOrFail($dispatchId);

        $request->validate([
            'pickup_location' => 'required|string|max:255',
            'delivery_location' => 'required|string|max:255',
            'dispatch_date' => 'required|date',
            'dispatch_time' => 'required|date_format:H:i',
            'description' => 'nullable|string|max:1000',
        ], [
            'pickup_location.required' => 'Pickup location is required.',
            'delivery_location.required' => 'Delivery location is required.',
            'dispatch_date.required' => 'Dispatch date is required.',
            'dispatch_time.required' => 'Dispatch time is required.',
        ]);

        $dispatch->update([
            'pickup_location' => $request->pickup_location,
            'delivery_location' => $request->delivery_location,
            'dispatch_date' => $request->dispatch_date,
            'dispatch_time' => $request->dispatch_time,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.dispatches')->with('success', 'Dispatch updated successfully!');
    }

    public function settings()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.settings', compact('admin'));
    }

    public function updateSettings(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'password' => 'nullable|min:6|confirmed',
        ], [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email.',
            'email.unique' => 'This email is already in use.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $admin->update($updateData);

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully!');
    }
}