<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        return response()
            ->view('user.auth.login')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'The email field is mandatory.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'The password field is mandatory.',
            'password.min' => 'Password must be at least 6 characters long.',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();
            
            if (!$user->is_verified) {
                Auth::guard('web')->logout();
                return back()->with('error', 'Please verify your email before logging in.');
            }

            return redirect()->route('user.dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
        }

        return back()->with('error', 'Invalid credentials provided.')->withInput();
    }

    public function showSignup()
    {
        return view('user.auth.signup');
    }

    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'password' => 'required|min:6|confirmed',
            'g-recaptcha-response' => 'required',
        ], [
            'name.required' => 'The name field is mandatory.',
            'email.required' => 'The email field is mandatory.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'phone.required' => 'The phone number is mandatory.',
            'password.required' => 'The password field is mandatory.',
            'password.min' => 'Password must be at least 6 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification.',
        ]);

        // Verify reCAPTCHA
        $recaptchaSecret = config('services.recaptcha.secret_key');
        $recaptchaResponse = $request->input('g-recaptcha-response');
        
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}");
        $responseData = json_decode($response);

        if (!$responseData->success) {
            return back()->with('error', 'reCAPTCHA verification failed. Please try again.')->withInput();
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_verified' => false,
        ]);

        // Generate and send OTP
        $otp = rand(100000, 999999);
        
        Otp::create([
            'email' => $user->email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
            'is_used' => false,
        ]);

        // Send OTP via email
        Mail::send('emails.otp', ['otp' => $otp, 'name' => $user->name], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Email Verification OTP - Dispatch Scheduling System');
        });

        session(['otp_email' => $user->email]);

        return redirect()->route('user.verify.otp')->with('success', 'Registration successful! Please check your email for OTP verification.');
    }

    public function showVerifyOtp()
    {
        if (!session('otp_email')) {
            return redirect()->route('user.signup');
        }

        return view('user.auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ], [
            'otp.required' => 'Please enter the OTP sent to your email.',
            'otp.digits' => 'OTP must be exactly 6 digits.',
        ]);

        $email = session('otp_email');
        
        $otpRecord = Otp::where('email', $email)
                        ->where('otp', $request->otp)
                        ->where('is_used', false)
                        ->where('expires_at', '>', Carbon::now())
                        ->first();

        if (!$otpRecord) {
            return back()->with('error', 'Invalid or expired OTP. Please try again.');
        }

        // Mark OTP as used
        $otpRecord->update(['is_used' => true]);

        // Verify user
        $user = User::where('email', $email)->first();
        $user->update([
            'is_verified' => true,
            'email_verified_at' => Carbon::now(),
        ]);

        session()->forget('otp_email');

        return redirect()->route('user.login')->with('success', 'Email verified successfully! You can now login.');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        
        // Invalidate the session
        $request->session()->invalidate();
        
        // Regenerate CSRF token for next request
        $request->session()->regenerateToken();
        
        // Explicitly delete the session from the database
        $request->session()->flush();
        
        // Return response with cache control headers to prevent back button
        return redirect()->route('user.login')
            ->with('success', 'Logged out successfully.')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}