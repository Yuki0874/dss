<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Dispatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web')->user();
        
        // Get user's dispatches
        $totalDispatches = Dispatch::where('user_id', $user->id)->count();
        $pendingDispatches = Dispatch::where('user_id', $user->id)->where('status', 'pending')->count();
        $completedDispatches = Dispatch::where('user_id', $user->id)->where('status', 'completed')->count();
        
        // Get recent dispatches
        $recentDispatches = Dispatch::where('user_id', $user->id)
            ->with('vehicles')
            ->latest()
            ->paginate(10);

        return view('user.dashboard', compact(
            'totalDispatches',
            'pendingDispatches',
            'completedDispatches',
            'recentDispatches'
        ));
    }

    public function createDispatch()
    {
        return view('user.create-dispatch');
    }

    public function storeDispatch(Request $request)
    {
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

        $user = Auth::guard('web')->user();

        Dispatch::create([
            'user_id' => $user->id,
            'pickup_location' => $request->pickup_location,
            'delivery_location' => $request->delivery_location,
            'dispatch_date' => $request->dispatch_date,
            'dispatch_time' => $request->dispatch_time,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Dispatch request created successfully!');
    }

    public function updateDispatch(Request $request, $dispatchId)
    {
        $dispatch = Dispatch::findOrFail($dispatchId);
        $user = Auth::guard('web')->user();

        // Check if user owns this dispatch and it's still pending
        if ($dispatch->user_id !== $user->id || $dispatch->status !== 'pending') {
            return back()->with('error', 'You cannot edit this dispatch.');
        }

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

        return redirect()->route('user.dashboard')->with('success', 'Dispatch updated successfully!');
    }

    public function cancelDispatch($dispatchId)
    {
        $dispatch = Dispatch::findOrFail($dispatchId);
        $user = Auth::guard('web')->user();

        // Check if user owns this dispatch and it's pending
        if ($dispatch->user_id !== $user->id || $dispatch->status !== 'pending') {
            return back()->with('error', 'You cannot cancel this dispatch.');
        }

        $dispatch->update(['status' => 'cancelled']);

        return redirect()->route('user.dashboard')->with('success', 'Dispatch cancelled successfully!');
    }

    public function deleteDispatch($dispatchId)
    {
        $dispatch = Dispatch::findOrFail($dispatchId);
        $user = Auth::guard('web')->user();

        // Check if user owns this dispatch and it's pending
        if ($dispatch->user_id !== $user->id || $dispatch->status !== 'pending') {
            return back()->with('error', 'You cannot delete this dispatch.');
        }

        // Detach vehicles before deleting
        $dispatch->vehicles()->detach();
        $dispatch->delete();

        return redirect()->route('user.dashboard')->with('success', 'Dispatch deleted successfully!');
    }
}
