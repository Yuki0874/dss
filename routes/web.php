<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\AuthController as UserAuthController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

// Home Route
Route::get('/', function () {
    return view('welcome');
});

// User Routes
Route::prefix('user')->name('user.')->group(function () {
    
    // Guest routes (not authenticated)
    Route::middleware(['guest:web'])->group(function () {
        Route::get('login', [UserAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [UserAuthController::class, 'login'])->name('login.submit');
        Route::get('signup', [UserAuthController::class, 'showSignup'])->name('signup');
        Route::post('signup', [UserAuthController::class, 'signup'])->name('signup.submit');
        Route::get('verify-otp', [UserAuthController::class, 'showVerifyOtp'])->name('verify.otp');
        Route::post('verify-otp', [UserAuthController::class, 'verifyOtp'])->name('verify.otp.submit');
    });

    // Authenticated user routes
    Route::middleware(['user'])->group(function () {
        Route::get('dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::get('create-dispatch', [UserDashboardController::class, 'createDispatch'])->name('create.dispatch');
        Route::post('create-dispatch', [UserDashboardController::class, 'storeDispatch'])->name('store.dispatch');
        Route::put('dispatch/{dispatch}/edit', [UserDashboardController::class, 'updateDispatch'])->name('update.dispatch');
        Route::post('dispatch/{dispatch}/cancel', [UserDashboardController::class, 'cancelDispatch'])->name('cancel.dispatch');
        Route::delete('dispatch/{dispatch}', [UserDashboardController::class, 'deleteDispatch'])->name('delete.dispatch');
        Route::post('logout', [UserAuthController::class, 'logout'])->name('logout');
    });
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    
    // Guest routes (not authenticated)
    Route::middleware(['guest:admin'])->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    // Authenticated admin routes
    Route::middleware(['admin'])->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('dispatches', [AdminDashboardController::class, 'dispatches'])->name('dispatches');
        Route::get('vehicles', [AdminDashboardController::class, 'vehicles'])->name('vehicles');
        Route::get('settings', [AdminDashboardController::class, 'settings'])->name('settings');
        Route::post('settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');
        Route::post('assign-vehicle/{dispatch}', [AdminDashboardController::class, 'assignVehicle'])->name('assign.vehicle');
        Route::put('dispatch/{dispatch}/edit', [AdminDashboardController::class, 'editDispatch'])->name('edit.dispatch');
        Route::post('complete-dispatch/{dispatch}', [AdminDashboardController::class, 'completeDispatch'])->name('complete.dispatch');
        Route::put('vehicle/{vehicle}/edit', [AdminDashboardController::class, 'updateVehicle'])->name('update.vehicle');
        Route::delete('vehicle/{vehicle}', [AdminDashboardController::class, 'deleteVehicle'])->name('delete.vehicle');
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});

Route::get('/setup-database', function () {
    if (env('APP_ENV') !== 'production') {
        return 'Not allowed';
    }
    
    Artisan::call('migrate', ['--force' => true]);
    Artisan::call('db:seed', ['--force' => true]);
    
    return 'Database setup complete! DELETE THIS ROUTE NOW!';
})->middleware('web');