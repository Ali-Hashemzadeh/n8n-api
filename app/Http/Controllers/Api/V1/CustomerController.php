<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCustomerRequest;
use App\Http\Requests\Api\V1\UpdateCustomerRequest;
use App\Http\Resources\Api\V1\CustomerResource;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 * name="Customers",
 * description="API Endpoints for managing customers"
 * )
 */
class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;

        // Ensure user is logged in
        $this->middleware('auth:sanctum');
    }

    /**
     * Check if the current user (Admin) has access to this specific customer.
     * Super-Admins always pass.
     */
    protected function authorizeCustomerAccess(Customer $customer)
    {
        $user = Auth::user();

        if ($user->hasRole('Super-Admin')) {
            return;
        }

        // Check if the customer belongs to the user's company
        $exists = $customer->companies()
            ->where('companies.id', $user->company_id)
            ->exists();

        if (!$exists) {
            abort(403, 'You do not have access to this customer.');
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/customers",
     * summary="List customers (Scoped by Company)",
     * tags={"Customers"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     * @OA\Parameter(name="per_page", in="query", example=15,@OA\Schema(type="integer")),
     * @OA\Parameter(name="search", in="query", description="Search by name/phone", @OA\Schema(type="string")),
     * @OA\Response(
     * response=200,
     * description="List of customers",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CustomerResource"))
     * )
     * )
     */
    public function index(Request $request)
    {
        // Add a permission check if you want, e.g., $this->authorize('see-customers');
        $customers = $this->customerService->getAll($request->all());
        return CustomerResource::collection($customers);
    }

    /**
     * @OA\Post(
     * path="/api/v1/customers",
     * summary="Create a new customer",
     * tags={"Customers"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "phone"},
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="phone", type="string"),
     * @OA\Property(property="email", type="string", format="email")
     * )
     * ),
     * @OA\Response(response=201, description="Customer created", @OA\JsonContent(ref="#/components/schemas/CustomerResource"))
     * )
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = $this->customerService->create($request->validated());

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     * path="/api/v1/customers/{customer}",
     * summary="Show a single customer",
     * tags={"Customers"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="customer", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Customer details")
     * )
     */
    public function show(Customer $customer)
    {
        $this->authorizeCustomerAccess($customer);
        return new CustomerResource($customer);
    }

    /**
     * @OA\Put(
     * path="/api/v1/customers/{customer}",
     * summary="Update a customer",
     * tags={"Customers"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="customer", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * @OA\JsonContent(
     * @OA\Property(property="name", type="string"),
     * @OA\Property(property="phone", type="string")
     * )
     * ),
     * @OA\Response(response=200, description="Customer updated")
     * )
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $this->authorizeCustomerAccess($customer);

        $updatedCustomer = $this->customerService->update($customer, $request->validated());

        return new CustomerResource($updatedCustomer);
    }

    /**
     * @OA\Delete(
     * path="/api/v1/customers/{customer}",
     * summary="Delete (or detach) a customer",
     * description="Admins only detach from their company. Super-Admins delete the record.",
     * tags={"Customers"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="customer", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=204, description="No content")
     * )
     */
    public function destroy(Customer $customer): Response
    {
        $this->authorizeCustomerAccess($customer);

        $this->customerService->delete($customer);

        return response()->noContent();
    }
}
