<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCompanyRequest;
use App\Http\Requests\Api\V1\UpdateCompanyRequest;
use App\Http\Resources\Api\V1\CompanyResource;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 * name="Companies",
 * description="API Endpoints for managing companies (Super-Admin only)"
 * )
 */
class CompanyController extends Controller
{
    /**
     * The service layer for handling company logic.
     */
    protected CompanyService $companyService;

    /**
     * Inject the service layer.
     */
    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * @OA\Get(
     * path="/api/v1/companies",
     * summary="Get a paginated list of all companies",
     * tags={"Companies"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(
     * response=200,
     * description="List of companies",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CompanyResource"))
     * ),
     * @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        // Check if user can view any companies (Super-Admin)
        $this->authorize('viewAny', Company::class);

        // Use the service to get paginated results
        $companies = $this->companyService->getAll();
//        $companies->load('serviceTypes');

        // Return a collection of resources
        return CompanyResource::collection($companies);
    }

    /**
     * @OA\Post(
     * path="/api/v1/companies",
     * summary="Create a new company",
     * tags={"Companies"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * description="Company data",
     * @OA\JsonContent(
     * required={"name"},
     * @OA\Property(property="name", type="string", example="New Clinic Inc.")
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Company created successfully",
     * @OA\JsonContent(ref="#/components/schemas/CompanyResource")
     * ),
     * @OA\Response(response=403, description="Forbidden"),
     * @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        // Check if user can create companies (Super-Admin)
        $this->authorize('create', Company::class);

        // The request is already validated by StoreCompanyRequest
        $company = $this->companyService->create($request->validated());

        // Return the new resource with a 201 status code
        return (new CompanyResource($company))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     * path="/api/v1/companies/{company}",
     * summary="Get a single company's details",
     * tags={"Companies"},
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
     * description="Company details",
     * @OA\JsonContent(ref="#/components/schemas/CompanyResource")
     * ),
     * @OA\Response(response=403, description="Forbidden"),
     * @OA\Response(response=404, description="Company not found")
     * )
     */
    public function show(Company $company): CompanyResource
    {
        // Check if user can view this company (Super-Admin)
        $this->authorize('view', $company);

        // Optional: Load relationships if you want them
        // $company->load('users', 'serviceTypes');

        // Return the resource
        return new CompanyResource($company);
    }

    /**
     * @OA\Put(
     * path="/api/v1/companies/{company}",
     * summary="Update a company",
     * tags={"Companies"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="company",
     * in="path",
     * required=true,
     * description="The ID of the company",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * description="Company data",
     * @OA\JsonContent(
     * @OA\Property(property="name", type="string", example="Updated Clinic Inc.")
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Company updated successfully",
     * @OA\JsonContent(ref="#/components/schemas/CompanyResource")
     * ),
     * @OA\Response(response=403, description="Forbidden"),
     * @OA\Response(response=404, description="Company not found"),
     * @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateCompanyRequest $request, Company $company): CompanyResource
    {
        // Check if user can update this company (Super-Admin)
        $this->authorize('update', $company);

        // The request is already validated by UpdateCompanyRequest
        $updatedCompany = $this->companyService->update(
            $company,
            $request->validated()
        );

        // Return the updated resource
        return new CompanyResource($updatedCompany);
    }

    /**
     * @OA\Delete(
     * path="/api/v1/companies/{company}",
     * summary="Delete a company",
     * tags={"Companies"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="company",
     * in="path",
     * required=true,
     * description="The ID of the company",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="No Content. Company deleted successfully."
     * ),
     * @OA\Response(response=403, description="Forbidden"),
     * @OA\Response(response=404, description="Company not found")
     * )
     */
    public function destroy(Company $company): Response
    {
        // Check if user can delete this company (Super-Admin)
        $this->authorize('delete', $company);

        // Use the service to delete the model
        $this->companyService->delete($company);

        // Return a 204 No Content response
        return response()->noContent();
    }
}
