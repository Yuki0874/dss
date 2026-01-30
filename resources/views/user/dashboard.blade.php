@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2><i class="fas fa-home"></i> Dashboard</h2>
        <p class="text-muted">Welcome, {{ Auth::guard('web')->user()->name }}!</p>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('user.create.dispatch') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> New Dispatch
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Dispatches</h6>
                        <h2>{{ $totalDispatches }}</h2>
                    </div>
                    <i class="fas fa-chart-bar fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Pending</h6>
                        <h2>{{ $pendingDispatches }}</h2>
                    </div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Completed</h6>
                        <h2>{{ $completedDispatches }}</h2>
                    </div>
                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">In Progress</h6>
                        <h2>{{ $totalDispatches - $pendingDispatches - $completedDispatches }}</h2>
                    </div>
                    <i class="fas fa-truck fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Dispatches -->
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-list"></i> Recent Dispatches</h5>
    </div>
    <div class="card-body">
        @if($recentDispatches->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Vehicles</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentDispatches as $dispatch)
                            <tr>
                                <td><strong>#{{ $dispatch->id }}</strong></td>
                                <td>{{ \Illuminate\Support\Str::limit($dispatch->pickup_location, 15) }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($dispatch->delivery_location, 15) }}</td>
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
                                    @if($dispatch->status === 'pending')
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                                data-bs-target="#editDispatch{{ $dispatch->id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    @endif
                                    @if($dispatch->status === 'pending')
                                        <form action="{{ route('user.cancel.dispatch', $dispatch->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this dispatch?')">
                                                <i class="fas fa-times"></i> Cancel
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
                {{ $recentDispatches->links() }}
            </div>

            <!-- Dispatch Detail Modals -->
            @foreach($recentDispatches as $dispatch)
                <div class="modal fade" id="dispatchDetail{{ $dispatch->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Dispatch #{{ $dispatch->id }} Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
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
                                @if($dispatch->status === 'pending')
                                    <form action="{{ route('user.delete.dispatch', $dispatch->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this dispatch? This action cannot be undone.')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Dispatch Modal -->
                @if($dispatch->status === 'pending')
                    <div class="modal fade" id="editDispatch{{ $dispatch->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Dispatch #{{ $dispatch->id }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('user.update.dispatch', $dispatch->id) }}" method="POST">
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
                                        <div class="mb-3">
                                            <label for="dispatch_date{{ $dispatch->id }}" class="form-label">Date</label>
                                            <input type="date" class="form-control" id="dispatch_date{{ $dispatch->id }}" 
                                                   name="dispatch_date" value="{{ $dispatch->dispatch_date->format('Y-m-d') }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="dispatch_time{{ $dispatch->id }}" class="form-label">Time</label>
                                            <input type="time" class="form-control" id="dispatch_time{{ $dispatch->id }}" 
                                                   name="dispatch_time" value="{{ $dispatch->dispatch_time->format('H:i') }}" required>
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
                @endif
            @endforeach
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No dispatches yet. 
                <a href="{{ route('user.create.dispatch') }}">Create one now!</a>
            </div>
        @endif
    </div>
</div>
@endsection
