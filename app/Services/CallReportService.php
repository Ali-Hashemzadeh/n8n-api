<?php

namespace App\Services;

use App\Models\CallReport;
use App\Models\Customer;
use App\Models\User; // <-- ADD THIS
use App\Models\Company; // <-- ADD THIS
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator; // <-- ADD THIS
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // <-- ADD THIS

class CallReportService
{
    /**
     * Create a new call report from the n8n intake.
     * This now includes "find or create" logic for the customer.
     *
     * @param array $data Validated data from StoreCallReportRequest
     * @return CallReport
     * @throws \Exception
     */
    public function createReport(array $data): CallReport
    {
        // Extract profile data
        $profile = $data['profile'];

        // Use a database transaction to ensure this either all
        // succeeds or all fails together.
        return DB::transaction(function () use ($data, $profile) {

            // 1. Find or Create the global Customer based on phone number
            $customer = Customer::firstOrCreate(
                ['phone' => $profile['phone']],
                [
                    'name' => $profile['name'],
                    'lastname' => $profile['lastname'] ?? null,
                    'email' => $profile['email'] ?? null,
                ]
            );

            // 2. Link this customer to the company (if not already linked)
            // This 'attach' will do nothing if the link already exists,
            // which is perfect for a many-to-many relationship.
            $customer->companies()->attach($data['company_id']);

            // 3. Create the Call Report
            $callReport = CallReport::create([
                'company_id' => $data['company_id'],
                'customer_id' => $customer->id,
                'summary' => $data['text'],
                'conversation' => $data['json'],
                'metadata' => $data['meta'] ?? null,
                'state' => $data['state'],
            ]);

            // 4. Set the timestamp (if n8n provided one)
            if (!empty($data['timestamp'])) {
                $callReport->created_at = Carbon::parse($data['timestamp']);
                $callReport->save();
            }

            return $callReport;
        });
    }

    /**
     * Get a paginated list of call reports.
     *
     * @param array $filters (e.g., state, date_from, date_to, search)
     * @param Company|null $company (If provided, scope results to this company)
     * @return LengthAwarePaginator
     */
    public function getReports(array $filters = [], Company $company = null): LengthAwarePaginator
    {
        /** @var User $user */
        $user = Auth::user();

        // Start the query, always loading relationships
        $query = CallReport::query()->with(['customer', 'company']);

        // --- FILTERING ---
        // 1. Scope by Company (if provided)
        if ($company) {
            $query->where('company_id', $company->id);
        }
        // 2. Or, if user is NOT a Super-Admin, scope to their own company
        elseif (!$user->hasRole('Super-Admin')) {
            $query->where('company_id', $user->company_id);
        }

        // 3. Filter by state (e.g., ?state=confirmed)
        if (!empty($filters['state'])) {
            $query->where('state', $filters['state']);
        }

        // 4. Filter by date range (e.g., ?date_from=...&date_to=...)
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // 5. Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            // This searches the customer's phone/name/email OR the call summary
            $query->where(function($q) use ($search) {
                $q->where('summary', 'like', "%{$search}%")
                    ->orWhereHas('customer', function($q_customer) use ($search) {
                        $q_customer->where('phone', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Order by the most recent call first
        $query->latest();

        // Return paginated results
        return $query->paginate(15)->withQueryString();
    }
}
