@extends('layouts.app')

@section('title', 'Manage Vehicles')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-truck"></i> Manage Vehicles</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($vehicles->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Vehicle Number</th>
                            <th>Type</th>
                            <th>Driver Name</th>
                            <th>Driver Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicles as $vehicle)
                            <tr>
                                <td>
                                    <strong>{{ $vehicle->vehicle_number }}</strong>
                                </td>
                                <td>{{ $vehicle->vehicle_type }}</td>
                                <td>{{ $vehicle->driver_name }}</td>
                                <td>{{ $vehicle->driver_phone }}</td>
                                <td>
                                    @if($vehicle->status === 'available')
                                        <span class="badge bg-success">Available</span>
                                    @elseif($vehicle->status === 'in-use')
                                        <span class="badge bg-warning">In Use</span>
                                    @else
                                        <span class="badge bg-danger">Maintenance</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" 
                                            data-bs-target="#viewVehicle{{ $vehicle->id }}">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                            data-bs-target="#editVehicle{{ $vehicle->id }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.delete.vehicle', $vehicle->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this vehicle?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $vehicles->links() }}
            </div>

            <!-- View and Edit Vehicle Modals -->
            @foreach($vehicles as $vehicle)
                <!-- View Modal -->
                <div class="modal fade" id="viewVehicle{{ $vehicle->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Vehicle #{{ $vehicle->vehicle_number }} Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Vehicle Number:</strong> {{ $vehicle->vehicle_number }}</p>
                                <p><strong>Vehicle Type:</strong> {{ $vehicle->vehicle_type }}</p>
                                <p><strong>Driver Name:</strong> {{ $vehicle->driver_name }}</p>
                                <p><strong>Driver Phone:</strong> {{ $vehicle->driver_phone }}</p>
                                <p><strong>Status:</strong> 
                                    @if($vehicle->status === 'available')
                                        <span class="badge bg-success">Available</span>
                                    @elseif($vehicle->status === 'in-use')
                                        <span class="badge bg-warning">In Use</span>
                                    @else
                                        <span class="badge bg-danger">Maintenance</span>
                                    @endif
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editVehicle{{ $vehicle->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Vehicle #{{ $vehicle->vehicle_number }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.update.vehicle', $vehicle->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="vehicle_number{{ $vehicle->id }}" class="form-label">Vehicle Number</label>
                                        <input type="text" class="form-control" id="vehicle_number{{ $vehicle->id }}" 
                                               name="vehicle_number" value="{{ $vehicle->vehicle_number }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="vehicle_type{{ $vehicle->id }}" class="form-label">Vehicle Type</label>
                                        <input type="text" class="form-control" id="vehicle_type{{ $vehicle->id }}" 
                                               name="vehicle_type" value="{{ $vehicle->vehicle_type }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="driver_name{{ $vehicle->id }}" class="form-label">Driver Name</label>
                                        <input type="text" class="form-control" id="driver_name{{ $vehicle->id }}" 
                                               name="driver_name" value="{{ $vehicle->driver_name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="driver_phone{{ $vehicle->id }}" class="form-label">Driver Phone</label>
                                        <input type="tel" class="form-control" id="driver_phone{{ $vehicle->id }}" 
                                               name="driver_phone" value="{{ $vehicle->driver_phone }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="status{{ $vehicle->id }}" class="form-label">Status</label>
                                        <select class="form-select" id="status{{ $vehicle->id }}" name="status" required>
                                            <option value="available" {{ $vehicle->status === 'available' ? 'selected' : '' }}>Available</option>
                                            <option value="in-use" {{ $vehicle->status === 'in-use' ? 'selected' : '' }}>In Use</option>
                                            <option value="maintenance" {{ $vehicle->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        </select>
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
                <i class="fas fa-info-circle"></i> No vehicles found.
            </div>
        @endif
    </div>
</div>
@endsection
