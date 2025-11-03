<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreServiceTypeRequest;
use App\Http\Requests\Api\V1\UpdateServiceTypeRequest;
use App\Http\Resources\Api\V1\ServiceTypeResource;
use App\Models\Company;
use App\Models\ServiceType;
use App\Services\ServiceTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * @OA\Schema(
 * schema="ServiceTypeResource",
 * title="ServiceTypeResource",
 * description="Service Type resource model",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="Haircut"),
 * @OA\Property(property="company_id", type="integer", example=1),
 * @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-03T12:00:00.000000Z"),
 * @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-03T12:00:00.000000Z")
 * )
 */
class ServiceTypeController extends Controller
{
    /**
     * The service layer for handling service type logic.
     */
    protected ServiceTypeService $serviceTypeService;

    /**
     * Inject the service layer into the controller.
     */
    public function __construct(ServiceTypeService $serviceTypeService)
    {
        $this->serviceTypeService = $serviceTypeService;
    }

    /**
     * @OA\Get(
     * path="/api/v1/companies/{company}/service-types",
     * summary="Get service types for a company",
     * tags={"Service Types"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="company",
     * in="path",
     * required=true,
     * description="The ID of the company",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="List of service types",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ServiceTypeResource"))
     * ),
     * @OA\Response(response=403, description="Forbidden"),
     * @OA\Response(response=404, description="Company not found")
     * )
     */
    public function index(Company $company): AnonymousResourceCollection
    {
        // Check if the user is authorized to view service types for this company
        $this->authorize('viewAny', $company);

        // Use the service to get paginated results
        $serviceTypes = $this->serviceTypeService->getForCompany($company);

        // Return a collection of resources
        return ServiceTypeResource::collection($serviceTypes);
    }

    /**
     * @OA\Post(
     * path="/api/v1/companies/{company}/service-types",
     * summary="Create a new service type for a company",
     * tags={"Service Types"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="company",
     * in="path",
     * required=true,
     * description="The ID of the company",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="Service type data",
     * @OA\JsonContent(
     * required={"name"},
     * @OA\Property(property="name", type="string", example="Haircut")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Service type created successfully",
     * @OA\JsonContent(ref="#/components/schemas/ServiceTypeResource")
     * ),
     * @OA\Response(response=403, description="Forbidden"),
     * @OA\Response(response=404, description="Company not found"),
     * @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreServiceTypeRequest $request, Company $company): JsonResponse
    {
        // Check if the user is authorized to create a service type for this company
        $this->authorize('create', $company);

        // The request is already validated by StoreServiceTypeRequest
        $serviceType = $this->serviceTypeService->createForCompany(
            $company,
            $request->validated()
        );

        // Return the new resource with a 201 status code
        return (new ServiceTypeResource($serviceType))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     * path="/api/v1/service-types/{service_type}",
     * summary="Get a single service type",
     * tags={"Service Types"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="service_type",
     * in="path",
     * required=true,
     * description="The ID of the service type",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Service type details",
     * @OA\JsonContent(ref="#/components/schemas/ServiceTypeResource")
     * ),
     * @OA\Response(response=403, description="Forbidden"),
     * @OA\Response(response=404, description="Service type not found")
     * )
     */
    public function show(ServiceType $serviceType): ServiceTypeResource
    {
        // Check if the user is authorized to view this specific service type
        $this->authorize('view', $serviceType);

        // Return the resource
        return new ServiceTypeResource($serviceType);
    }

    /**
     * @OA\Put(
     * path="/api/v1/service-types/{service_type}",
     * summary="Update a service type",
     * tags={"Service Types"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="service_type",
     * in="path",
     * required=true,
     * description="The ID of the service type",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * description="Service type data",
     * @OA\JsonContent(
     * @OA\Property(property="name", type="string", example="Beard Trim")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Service type updated successfully",
     * @OA\JsonContent(ref="#/components/schemas/ServiceTypeResource")
     * ),
     * @OA\Response(response=403, description="Forbidden"),
     * @OA\Response(response=404, description="Service type not found"),
     * @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateServiceTypeRequest $request, ServiceType $serviceType): ServiceTypeResource
    {
        // Check if the user is authorized to update this service type
        $this->authorize('update', $serviceType);

        // The request is already validated by UpdateServiceTypeRequest
        $updatedServiceType = $this->serviceTypeService->update(
            $serviceType,
            $request->validated()
        );

        // Return the updated resource
        return new ServiceTypeResource($updatedServiceType);
    }

    /**
     * @OA\Delete(
     * path="/api/v1/service-types/{service_type}",
     * summary="Delete a service type",
     * tags={"Service Types"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="service_type",
     * in="path",
     * required=true,
     * description="The ID of the service type",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="No Content. Service type deleted successfully."
     * ),
     * @OA\Response(response=403, description="Forbidden"),
     * @OA\Response(response=404, description="Service type not found")
     * )
     */
    public function destroy(ServiceType $serviceType): Response
    {
        // Check if the user is authorized to delete this service type
        $this->authorize('delete', $serviceType);

        // Use the service to delete the model
        $this->serviceTypeService->delete($serviceType);

        // Return a 204 No Content response
        return response()->noContent();
    }
}


