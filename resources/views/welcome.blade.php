@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card mt-5">
            <div class="card-body p-5 text-center">
                <i class="fas fa-truck-moving fa-5x text-primary mb-4"></i>
                <h1 class="display-4 mb-3">Dispatch Scheduling System</h1>
                <p class="lead text-muted mb-4">
                    Efficient dispatch management solution for your business needs
                </p>
                
                <div class="row mt-5">
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-primary">
                            <div class="card-body p-4">
                                <i class="fas fa-user fa-3x text-primary mb-3"></i>
                                <h3>For Users</h3>
                                <p class="text-muted">Create dispatch requests and track your deliveries</p>
                                <a href="{{ route('user.login') }}" class="btn btn-primary me-2">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </a>
                                <a href="{{ route('user.signup') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-user-plus"></i> Sign Up
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-danger">
                            <div class="card-body p-4">
                                <i class="fas fa-user-shield fa-3x text-danger mb-3"></i>
                                <h3>For Admins</h3>
                                <p class="text-muted">Manage dispatches, vehicles and monitor operations</p>
                                <a href="{{ route('admin.login') }}" class="btn btn-danger">
                                    <i class="fas fa-sign-in-alt"></i> Admin Login
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-5">
                    <div class="col-md-4 mb-3">
                        <div class="p-3">
                            <i class="fas fa-clock fa-2x text-primary mb-3"></i>
                            <h5>Real-Time Tracking</h5>
                            <p class="text-muted small">Monitor your dispatches in real-time</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3">
                            <i class="fas fa-shield-alt fa-2x text-primary mb-3"></i>
                            <h5>Secure & Verified</h5>
                            <p class="text-muted small">Email verification and secure authentication</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="p-3">
                            <i class="fas fa-chart-line fa-2x text-primary mb-3"></i>
                            <h5>Analytics Dashboard</h5>
                            <p class="text-muted small">Track performance with detailed analytics</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection