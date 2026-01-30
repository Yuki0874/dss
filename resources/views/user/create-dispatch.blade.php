@extends('layouts.app')

@section('title', 'Create Dispatch Request')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Create New Dispatch Request</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('user.store.dispatch') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pickup_location" class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Pickup Location
                            </label>
                            <input type="text" class="form-control @error('pickup_location') is-invalid @enderror" 
                                   id="pickup_location" name="pickup_location" 
                                   value="{{ old('pickup_location') }}" required>
                            @error('pickup_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="delivery_location" class="form-label">
                                <i class="fas fa-map-marker-alt"></i> Delivery Location
                            </label>
                            <input type="text" class="form-control @error('delivery_location') is-invalid @enderror" 
                                   id="delivery_location" name="delivery_location" 
                                   value="{{ old('delivery_location') }}" required>
                            @error('delivery_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="dispatch_date" class="form-label">
                                <i class="fas fa-calendar"></i> Dispatch Date
                            </label>
                            <input type="date" class="form-control @error('dispatch_date') is-invalid @enderror" 
                                   id="dispatch_date" name="dispatch_date" 
                                   value="{{ old('dispatch_date') }}" required>
                            @error('dispatch_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="dispatch_time" class="form-label">
                                <i class="fas fa-clock"></i> Dispatch Time
                            </label>
                            <input type="time" class="form-control @error('dispatch_time') is-invalid @enderror" 
                                   id="dispatch_time" name="dispatch_time" 
                                   value="{{ old('dispatch_time') }}" required>
                            @error('dispatch_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            <i class="fas fa-comment"></i> Description (Optional)
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection