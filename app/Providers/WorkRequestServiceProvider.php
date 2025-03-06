<?php

namespace App\Providers;

use App\Models\WorkRequest;
use App\Policies\WorkRequestPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class WorkRequestServiceProvider extends ServiceProvider
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
        Gate::policy(WorkRequest::class, WorkRequestPolicy::class);
    }
}
