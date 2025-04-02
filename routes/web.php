<?php

use App\Enums\UserRole;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OnboardingController;
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

    Route::get('/register', function () {
        return redirect()->route('onboarding.step1');
    })->name('onboarding');

    Route::get('/register/step1', [OnboardingController::class, 'showStep1'])->name('onboarding.step1');
    Route::post('/register/step1', [OnboardingController::class, 'processStep1']);
    
    Route::get('/register/step2', [OnboardingController::class, 'showStep2'])->name('onboarding.step2');
    Route::post('/register/step2', [OnboardingController::class, 'processStep2']);
    
    Route::get('/register/step3', [OnboardingController::class, 'showStep3'])->name('onboarding.step3');
    Route::post('/register/step3', [OnboardingController::class, 'processStep3']);
    
    Route::get('/register/step4', [OnboardingController::class, 'showStep4'])->name('onboarding.step4');
    Route::post('/register/step4', [OnboardingController::class, 'processStep4']);
    
    Route::get('/register/step5', [OnboardingController::class, 'showStep5'])->name('onboarding.step5');
    Route::post('/register/step5', [OnboardingController::class, 'processStep5']);
    
    Route::get('/register/step6', [OnboardingController::class, 'showStep6'])->name('onboarding.step6');
    Route::post('/register/step6', [OnboardingController::class, 'processStep6']);
    
    Route::post('/register/cancel', [OnboardingController::class, 'cancel'])->name('onboarding.cancel');
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

    Route::get("/profile", function () {
        return view("profile.index");
    })->name("profile");

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