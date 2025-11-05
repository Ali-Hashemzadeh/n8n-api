<?php

namespace App\Providers;

use App\Models\CallReport;
use App\Models\Company;
use App\Models\ServiceType;
use App\Policies\CallReportPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\ServiceTypePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ServiceType::class => ServiceTypePolicy::class,
        Company::class => CompanyPolicy::class,
        CallReport::class => CallReportPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
