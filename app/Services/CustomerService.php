<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class CustomerService
{
    /**
     * Get a paginated list of customers, scoped by user role.
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        /** @var User $user */
        $user = Auth::user();

        $query = Customer::query();

        // 1. Scoping Logic
        if (!$user->hasRole('Super-Admin')) {
            // If not Super-Admin, only show customers linked to the user's company
            $query->whereHas('companies', function (Builder $q) use ($user) {
                $q->where('companies.id', $user->company_id);
            });
        }

        // 2. Search Filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['per_page'])) {
            $perPage = $filters['per_page'];
        } else {
            $perPage = 15;
        }


        // 3. Ordering
        $query->latest();

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Create a new customer.
     */
    public function create(array $data): Customer
    {
        /** @var User $user */
        $user = Auth::user();

        // 1. Create or Find the Customer (Phone must be unique globally usually, but let's assume standard create)
        // If you want to prevent duplicates, use firstOrCreate, but for a CRUD 'store' we usually try to create.
        $customer = Customer::create([
            'name' => $data['name'],
            'lastname' => $data['lastname'] ?? '', // Handle nullable lastname if your DB allows
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
        ]);

        // 2. Link to Company
        if ($user->hasRole('Super-Admin')) {
            // If Super-Admin provided specific company_ids, attach them
            if (!empty($data['company_ids'])) {
                $customer->companies()->attach($data['company_ids']);
            }
        } elseif ($user->company_id) {
            // If Admin, automatically link to their own company
            $customer->companies()->attach($user->company_id);
        }

        return $customer;
    }

    /**
     * Update a customer.
     */
    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);
        return $customer;
    }

    /**
     * Delete (or Detach) a customer.
     */
    public function delete(Customer $customer): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->hasRole('Super-Admin')) {
            // Super-Admin: Hard delete the customer record from DB
            $customer->delete();
        } else {
            // Admin: ONLY detach from their company.
            // The customer might belong to other companies, so we don't delete the record.
            if ($user->company_id) {
                $customer->companies()->detach($user->company_id);
            }
        }
    }
}
