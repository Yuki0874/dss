@extends('layouts.app')

@section('title', 'All Dispatches')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-list"></i> All Dispatches</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($dispatches->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Pickup Location</th>
                            <th>Delivery Location</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Vehicles</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dispatches as $dispatch)
                            <tr>
                                <td>#{{ $dispatch->id }}</td>
                                <td>
                                    <strong>{{ $dispatch->user->name }}</strong><br>
                                    <small class="text-muted">{{ $dispatch->user->email }}</small>
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($dispatch->pickup_location, 20) }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($dispatch->delivery_location, 20) }}</td>
                                <td>{{ $dispatch->dispatch_date->format('M d, Y') }}</td>
                                <td>{{ $dispatch->dispatch_time->format('H:i A') }}</td>
                                <td>
                                    @if($dispatch->status === 'pending')
                                        <span class="badge bg-secondary">Pending</span>
                                    @elseif($dispatch->status === 'assigned')
                                        <span class="badge bg-info">Assigned</span>
                                    @elseif($dispatch->status === 'in-progress')
                                        <span class="badge bg-warning">In Progress</span>
                                    @elseif($dispatch->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    @if($dispatch->vehicles->count() > 0)
                                        <span class="badge bg-primary">{{ $dispatch->vehicles->count() }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                            data-bs-target="#dispatchDetail{{ $dispatch->id }}">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                            data-bs-target="#editDispatch{{ $dispatch->id }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    @if($dispatch->status === 'pending')
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                                data-bs-target="#assignVehicle{{ $dispatch->id }}">
                                            <i class="fas fa-truck"></i> Assign
                                        </button>
                                    @elseif($dispatch->status === 'assigned')
                                        <form action="{{ route('admin.complete.dispatch', $dispatch->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Complete
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $dispatches->links() }}
            </div>

            <!-- Dispatch Detail and Assign Vehicle Modals -->
            @foreach($dispatches as $dispatch)
                <!-- Detail Modal -->
                <div class="modal fade" id="dispatchDetail{{ $dispatch->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Dispatch #{{ $dispatch->id }} Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>User:</strong> {{ $dispatch->user->name }} ({{ $dispatch->user->email }})</p>
                                <p><strong>Pickup Location:</strong> {{ $dispatch->pickup_location }}</p>
                                <p><strong>Delivery Location:</strong> {{ $dispatch->delivery_location }}</p>
                                <p><strong>Date:</strong> {{ $dispatch->dispatch_date->format('M d, Y') }}</p>
                                <p><strong>Time:</strong> {{ $dispatch->dispatch_time->format('H:i A') }}</p>
                                <p><strong>Status:</strong> 
                                    @if($dispatch->status === 'pending')
                                        <span class="badge bg-secondary">Pending</span>
                                    @elseif($dispatch->status === 'assigned')
                                        <span class="badge bg-info">Assigned</span>
                                    @elseif($dispatch->status === 'in-progress')
                                        <span class="badge bg-warning">In Progress</span>
                                    @elseif($dispatch->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </p>
                                @if($dispatch->description)
                                    <p><strong>Description:</strong> {{ $dispatch->description }}</p>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assign Vehicle Modal -->
                <div class="modal fade" id="assignVehicle{{ $dispatch->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Assign Vehicle to Dispatch #{{ $dispatch->id }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.assign.vehicle', $dispatch->id) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="vehicle_id{{ $dispatch->id }}" class="form-label">Select Vehicle</label>
                                        <select class="form-select" name="vehicle_id" id="vehicle_id{{ $dispatch->id }}" required>
                                            <option value="">Choose a vehicle...</option>
                                            @php
                                                $availableVehicles = \App\Models\Vehicle::where('status', 'available')->get();
                                            @endphp
                                            @forelse($availableVehicles as $vehicle)
                                                <option value="{{ $vehicle->id }}">
                                                    {{ $vehicle->vehicle_number }} ({{ $vehicle->vehicle_type }}) - {{ $vehicle->driver_name }}
                                                </option>
                                            @empty
                                                <option value="" disabled>No available vehicles</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Assign Vehicle</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Edit Dispatch Modal -->
                <div class="modal fade" id="editDispatch{{ $dispatch->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Dispatch #{{ $dispatch->id }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.edit.dispatch', $dispatch->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="pickup_location{{ $dispatch->id }}" class="form-label">Pickup Location</label>
                                        <input type="text" class="form-control" id="pickup_location{{ $dispatch->id }}" 
                                               name="pickup_location" value="{{ $dispatch->pickup_location }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="delivery_location{{ $dispatch->id }}" class="form-label">Delivery Location</label>
                                        <input type="text" class="form-control" id="delivery_location{{ $dispatch->id }}" 
                                               name="delivery_location" value="{{ $dispatch->delivery_location }}" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="dispatch_date{{ $dispatch->id }}" class="form-label">Date</label>
                                                <input type="date" class="form-control" id="dispatch_date{{ $dispatch->id }}" 
                                                       name="dispatch_date" value="{{ $dispatch->dispatch_date->format('Y-m-d') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="dispatch_time{{ $dispatch->id }}" class="form-label">Time</label>
                                                <input type="time" class="form-control" id="dispatch_time{{ $dispatch->id }}" 
                                                       name="dispatch_time" value="{{ $dispatch->dispatch_time->format('H:i') }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description{{ $dispatch->id }}" class="form-label">Description (Optional)</label>
                                        <textarea class="form-control" id="description{{ $dispatch->id }}" 
                                                  name="description" rows="3">{{ $dispatch->description }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No dispatches found.
            </div>
        @endif
    </div>
</div>
@endsection
