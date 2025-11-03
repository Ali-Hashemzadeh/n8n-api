<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ServiceTypePolicy
{
    /**
     * Perform pre-authorization checks.
     * This 'before' method grants Super-Admins all permissions automatically.
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
     * This checks if a user can see the list of service types for a *specific company*.
     */
    public function viewAny(User $user, Company $company): bool
    {
        // User must have the permission AND belong to the company
        return $user->can('manage-service-types')
            && $user->company_id === $company->id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServiceType $serviceType): bool
    {
        // User must have the permission AND belong to the service type's company
        return $user->can('manage-service-types')
            && $user->company_id === $serviceType->company_id;
    }

    /**
     * Determine whether the user can create models.
     * This checks if a user can create a service type for a *specific company*.
     */
    public function create(User $user, Company $company): bool
    {
        // User must have the permission AND belong to the company
        return $user->can('manage-service-types')
            && $user->company_id === $company->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceType $serviceType): bool
    {
        // User must have the permission AND belong to the service type's company
        return $user->can('manage-service-types')
            && $user->company_id === $serviceType->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServiceType $serviceType): bool
    {
        // User must have the permission AND belong to the service type's company
        return $user->can('manage-service-types')
            && $user->company_id === $serviceType->company_id;
    }
}
