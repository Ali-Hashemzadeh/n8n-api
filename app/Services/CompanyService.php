<?php
// In app/Services/CompanyService.php

namespace App\Services;

use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompanyService
{
    /**
     * Get a paginated list of all companies.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        // You can add more logic here later, like search or filters
        return Company::query()->with('serviceTypes')->paginate($perPage);
    }

    /**
     * Create a new company.
     *
     * @param array $data Validated data from the request.
     * @return Company
     */
    public function create(array $data): Company
    {
        return Company::create($data);
    }

    /**
     * Update an existing company.
     *
     * @param Company $company The company to update.
     * @param array $data Validated data from the request.
     * @return Company
     */
    public function update(Company $company, array $data): Company
    {
        $company->update($data);
        return $company;
    }

    /**
     * Delete a company.
     *
     * @param Company $company The company to delete.
     * @return void
     */
    public function delete(Company $company): void
    {
        // The 'onDelete('cascade')' in the service_types migration
        // will handle deleting all its related service types.
        // We just need to delete the company.
        $company->delete();
    }
}
