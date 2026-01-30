@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card mt-5">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="fas fa-user-shield fa-4x text-danger"></i>
                    <h3 class="mt-3">Admin Login</h3>
                    <p class="text-muted">Administrative Access Only</p>
                </div>

                <form action="{{ route('admin.login.submit') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email Address
                        </label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-danger btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Admin Login
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="mb-0 text-muted small">
                            <i class="fas fa-info-circle"></i> Authorized personnel only
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection