<?php

use App\Enums\UserRole;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::post(
        '/authenticate', [LoginController::class, 'authenticate']
    )->name('authenticate');
});


Route::middleware(['auth', 'auth.session'])->group(function () {
    Route::get("/", function (Request $request) {
        $role = $request->user()->role;

        return match ($role) {
            UserRole::EMPLOYEE => view('employee.dashboard'),
            UserRole::HR => view('hr.dashboard'),
            UserRole::ADMIN => view('admin.dashboard'),
            default => abort(403),
        };
    })->name("dashboard");

    Route::get("/logout", [LoginController::class, 'logout'])->name("logout");
        
    Route::resource('attendance', AttendanceController::class)
        ->except(['edit']);
        
    Route::get('/employee/update-attendance', [EmployeeController::class, 'updateAttendanceStatus'])
        ->name("employee.updateAttendanceStatus");

    Route::resource('leave', LeaveRequestController::class);

    Route::patch('/leave/{id}/status', [LeaveRequestController::class, 'updateStatus']);
});