@extends('layouts.app')

@section('title', 'Verify OTP')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card mt-5">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="fas fa-check-circle fa-4x text-info"></i>
                    <h3 class="mt-3">Verify Email</h3>
                    <p class="text-muted">Enter the OTP sent to your email</p>
                </div>

                <form action="{{ route('user.verify.otp.submit') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="otp" class="form-label">
                            <i class="fas fa-key"></i> OTP Code
                        </label>
                        <input type="text" class="form-control @error('otp') is-invalid @enderror text-center" 
                               id="otp" name="otp" placeholder="000000" maxlength="6" required>
                        @error('otp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-info btn-lg">
                            <i class="fas fa-check"></i> Verify OTP
                        </button>
                    </div>

                    <div class="text-center text-muted small">
                        <p>OTP expires in 10 minutes</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
