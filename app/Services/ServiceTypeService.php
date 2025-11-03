<?php
// In app/Services/ServiceTypeService.php

namespace App\Services;

use App\Models\Company;
use App\Models\ServiceType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ServiceTypeService
{
    /**
     * Get a paginated list of service types for a specific company.
     *
     * @param Company $company The company to fetch service types for.
     * @param int $perPage The number of items to show per page.
     * @return LengthAwarePaginator
     */
    public function getForCompany(Company $company, int $perPage = 15): LengthAwarePaginator
    {
        // We use the relationship defined on the Company model
        // and paginate the results.
        return $company->serviceTypes()->paginate($perPage);
    }

    /**
     * Create a new service type for a specific company.
     *
     * @param Company $company The company to associate the new service type with.
     * @param array $data The validated data from the request.
     * @return ServiceType
     */
    public function createForCompany(Company $company, array $data): ServiceType
    {
        // We use the relationship to create the new service type.
        // This automatically sets the 'company_id' for us.
        return $company->serviceTypes()->create($data);
    }

    /**
     * Update an existing service type.
     *
     * @param ServiceType $serviceType The service type model to update.
     * @param array $data The validated data from the request.
     * @return ServiceType
     */
    public function update(ServiceType $serviceType, array $data): ServiceType
    {
        // We just call update on the model instance.
        $serviceType->update($data);

        // Return the updated model.
        return $serviceType;
    }

    /**
     * Delete a service type.
     *
     * @param ServiceType $serviceType The service type model to delete.
     * @return void
     */
    public function delete(ServiceType $serviceType): void
    {
        $serviceType->delete();
    }
}
