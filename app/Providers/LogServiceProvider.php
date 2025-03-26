<?php

namespace App\Providers;

use App\Models\Attendance;
use App\Models\CharacterReference;
use App\Models\Document;
use App\Models\EducationInformation;
use App\Models\Employee;
use App\Models\FamilyInformation;
use App\Models\LeaveRequest;
use App\Models\Log;
use App\Models\OjtInformation;
use App\Models\User;
use App\Models\WorkRequest;
use App\Observers\AttendanceObserver;
use App\Observers\CharacterReferenceObserver;
use App\Observers\DocumentObserver;
use App\Observers\EducationInformationObserver;
use App\Observers\EmployeeObserver;
use App\Observers\FamilyInformationObserver;
use App\Observers\LeaveRequestObserver;
use App\Observers\OjtInformationObserver;
use App\Observers\UserObserver;
use App\Observers\WorkRequestObserver;
use App\Policies\LogPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::policy(Log::class, LogPolicy::class);

        self::setupObservers();
    }

    private function setupObservers(): void
    {
        Attendance::observe(AttendanceObserver::class);
        CharacterReference::observe(CharacterReferenceObserver::class);
        Document::observe(DocumentObserver::class);
        EducationInformation::observe(EducationInformationObserver::class);
        Employee::observe(EmployeeObserver::class);
        FamilyInformation::observe(FamilyInformationObserver::class);
        LeaveRequest::observe(LeaveRequestObserver::class);
        OjtInformation::observe(OjtInformationObserver::class);
        User::observe(UserObserver::class);
        WorkRequest::observe(WorkRequestObserver::class);
    }
}
