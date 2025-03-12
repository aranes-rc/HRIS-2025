<?php

use App\Enums\UserRole;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserRoleSwitchController;
use App\Http\Controllers\WorkRequestController;
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
        $role = $request->user()->getActiveRole();

        return match ($role) {
            UserRole::EMPLOYEE->value => view('dashboard.employee'),
            UserRole::HR->value => view('dashboard.hr'),
            UserRole::ADMIN->value => view('dashboard.admin'),
            UserRole::GROUP_LEADER->value => view('dashboard.gl'),
            UserRole::TEAM_LEADER->value => view('dashboard.tl'),
            default => abort(403),
        };
    })->name("dashboard");

    Route::get("/logout", [LoginController::class, 'logout'])->name("logout");
        
    Route::resource('attendance', AttendanceController::class);

    Route::get('/attendance/export', function () {})->name('attendance.export');

    Route::resource('leave', LeaveRequestController::class);

    Route::patch('/leave/{id}/status', [LeaveRequestController::class, 'updateStatus'])
        ->name('leave.update.status');

    Route::resource('work-request', WorkRequestController::class);

    Route::resource('document', DocumentController::class);

    Route::post('/role/switch', [UserRoleSwitchController::class, 'switch'])
        ->middleware('role.switch')
        ->name('role.switch');
});