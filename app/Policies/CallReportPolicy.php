<?php

namespace App\Policies;

use App\Models\CallReport;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CallReportPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Super-Admin')) {
            return true;
        }

        return null; // Continue to other policy checks
    }

    /**
     * Determine whether the user can view any models.
     * (This is for the global /api/v1/call-reports)
     */
    public function viewAny(User $user): bool
    {
        // Only Super-Admin can see all reports from all companies
        return $user->hasRole('Super-Admin');
    }

    /**
     * Determine whether the user can view any models for a specific company.
     * (This is for /api/v1/companies/{company}/call-reports)
     */
    public function viewAnyForCompany(User $user, Company $company): bool
    {
        // User must have the permission AND belong to the company
        return $user->can('see-call-reports')
            && $user->company_id === $company->id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CallReport $callReport): bool
    {
        // User must have the permission AND belong to the report's company
        return $user->can('see-call-reports')
            && $user->company_id === $callReport->company_id;
    }

    /**
     * Determine whether the user can create models.
     * (Not used by users, only by n8n middleware)
     */
    public function create(User $user): bool
    {
        return false; // Users cannot create reports directly
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CallReport $callReport): bool
    {
        return false; // Reports are immutable
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CallReport $callReport): bool
    {
        return false; // Reports are immutable
    }
}
