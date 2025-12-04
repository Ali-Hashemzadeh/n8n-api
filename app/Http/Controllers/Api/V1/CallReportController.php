<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCallReportRequest;
use App\Http\Resources\Api\V1\CallReportResource;
use App\Models\CallReport; // <-- ADD THIS
use App\Models\Company; // <-- ADD THIS
use App\Services\CallReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; // <-- ADD THIS
use Illuminate\Http\Resources\Json\AnonymousResourceCollection; // <-- ADD THIS
use Illuminate\Http\Response;

/**
 * @OA\SecurityScheme(
 * securityScheme="n8nToken",
 * type="http",
 * scheme="bearer",
 * bearerFormat="Token",
 * description="Secret token for n8n service"
 * )
 * @OA\Tag(
 * name="Call Reports",
 * description="Endpoints for managing and viewing call reports"
 * )
 */
class CallReportController extends Controller
{
    /**
     * The service layer for handling call report logic.
     */
    protected CallReportService $callReportService;

    /**
     * Inject the service layer into the controller.
     */
    public function __construct(CallReportService $callReportService)
    {
        $this->callReportService = $callReportService;

        // Apply our n8n token auth middleware ONLY to the 'intake' method
        $this->middleware('n8n.token')->only(['intake']);

        // Apply Sanctum auth to all other dashboard-facing methods
        $this->middleware('auth:sanctum')->except(['intake']);
    }

    /**
     * @OA\Post(
     * path="/api/v1/call-reports/intake",
     * summary="Submit a new call report (n8n)",
     * tags={"Call Reports"},
     * security={{"n8nToken":{}}},
     * @OA\RequestBody(
     * required=true,
     * description="JSON payload from n8n",
     * @OA\JsonContent(
     * required={"company_id", "profile", "text", "json", "state"},
     * @OA\Property(property="company_id", type="integer", example=1),
     * @OA\Property(
     * property="profile",
     * type="object",
     * @OA\Property(property="phone", type="string", example="555-123-4567"),
     * @OA\Property(property="name", type="string", example="Jane"),
     * @OA\Property(property="lastname", type="string", example="Doe"),
     * @OA\Property(property="email", type="string", format="email", example="jane@example.com")
     * ),
     * @OA\Property(property="text", type="string", example="Customer confirmed appointment..."),
     * @OA\Property(
     * property="json",
     * type="object",
     * @OA\Property(
     * property="transcript",
     * type="array",
     * @OA\Items(
     * type="object",
     * @OA\Property(property="speaker", type="string", example="AI"),
     * @OA\Property(property="text", type="string", example="Hello, how can I help?")
     * )
     * )
     * ),
     * @OA\Property(
     * property="meta",
     * type="object",
     * @OA\Property(property="duration_seconds", type="integer", example=60)
     * ),
     * @OA\Property(property="state", type="string", enum={"confirmed", "failed", "unfinished"}, example="confirmed"),
     * @OA\Property(property="timestamp", type="string", format="date-time", example="2025-11-05T18:30:00Z"),
     * @OA\Property(
     * property="service_type_ids",
     * type="array",
     * @OA\Items(type="integer"),
     * example={1, 2}
     * )
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Report created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="Call report created."),
     * @OA\Property(property="data", type="object", @OA\Property(property="id", type="integer", example=123))
     * )
     * ),
     * @OA\Response(response=401, description="Unauthorized (Invalid n8n token)"),
     * @OA\Response(response=422, description="Validation error"),
     * @OA\Response(response=500, description="Server misconfigured (n8n token not set in .env)")
     * )
     */
    public function intake(StoreCallReportRequest $request): JsonResponse
    {
        // The request is already validated by StoreCallReportRequest
        $callReport = $this->callReportService->createReport($request->validated());

        return response()->json([
            'message' => 'Call report created successfully.',
            'data' => [
                'id' => $callReport->id,
            ]
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     * path="/api/v1/call-reports",
     * summary="List all call reports (Super-Admin only)",
     * tags={"Call Reports"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="page",
     * in="query",
     * description="Page number for pagination",
     * @OA\Schema(type="integer", example=1)
     * ),
     *  @OA\Parameter(
     *  name="per_page",
     *  in="query",
     *  description="number of call reports per page",
     *  @OA\Schema(type="integer", example=15)
     *  ),
     * @OA\Parameter(
     * name="state",
     * in="query",
     * description="Filter by state",
     * @OA\Schema(type="string", enum={"confirmed", "failed", "unfinished"})
     * ),
     * @OA\Parameter(
     * name="date_from",
     * in="query",
     * description="Filter by start date (YYYY-MM-DD)",
     * @OA\Schema(type="string", format="date", example="2025-01-01")
     * ),
     * @OA\Parameter(
     * name="date_to",
     * in="query",
     * description="Filter by end date (YYYY-MM-DD)",
     * @OA\Schema(type="string", format="date", example="2025-01-31")
     * ),
     * @OA\Parameter(
     * name="search",
     * in="query",
     * description="Search by customer phone/name/email or call summary",
     * @OA\Schema(type="string", example="555-123-4567")
     * ),
     * @OA\Response(
     * response=200,
     * description="A paginated list of call reports",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CallReportResource"))
     * ),
     * @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        // Check if user is authorized to see ALL reports
        $this->authorize('viewAny', CallReport::class);

        // Get filterable reports from the service
        $reports = $this->callReportService->getReports($request->all());

        return CallReportResource::collection($reports);
    }

    /**
     * @OA\Get(
     * path="/api/v1/companies/{company}/call-reports",
     * summary="List call reports for a specific company",
     * tags={"Call Reports"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="company",
     * in="path",
     * required=true,
     * description="The ID of the company",
     * @OA\Schema(type="integer")
     * ),
     *   @OA\Parameter(
     *   name="per_page",
     *   in="query",
     *   description="number of call reports per page",
     *   @OA\Schema(type="integer", example=15)
     *   ),
     * @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", example=1)),
     * @OA\Parameter(name="state", in="query", @OA\Schema(type="string", enum={"confirmed", "failed", "unfinished"})),
     * @OA\Parameter(name="date_from", in="query", @OA\Schema(type="string", format="date", example="2025-01-01")),
     * @OA\Parameter(name="date_to", in="query", @OA\Schema(type="string", format="date", example="2025-01-31")),
     * @OA\Parameter(name="search", in="query", @OA\Schema(type="string", example="555-123-4567")),
     * @OA\Response(
     * response=200,
     * description="A paginated list of call reports",
     * @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CallReportResource"))
     * ),
     * @OA\Response(response=403, description="Forbidden"),
     * @OA\Response(response=404, description="Company not found")
     * )
     */
    public function indexForCompany(Request $request, Company $company): AnonymousResourceCollection
    {
        // Check if user is authorized to see reports for THIS company
        $this->authorize('see-call-reports', [CallReport::class, $company]);

        // Get filterable reports, scoped to the company
        $reports = $this->callReportService->getReports($request->all(), $company);

        return CallReportResource::collection($reports);
    }

    /**
     * @OA\Get(
     * path="/api/v1/call-reports/{call_report}",
     * summary="Show a single call report",
     * tags={"Call Reports"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="call_report",
     * in="path",
     * required=true,
     * description="The ID of the call report",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Call report details",
     * @OA\JsonContent(ref="#/components/schemas/CallReportResource")
     * ),
     * @OA\Response(response=403, description="Forbidden"),
     * @OA\Response(response=404, description="Report not found")
     * )
     */
    public function show(CallReport $callReport): CallReportResource
    {
        // Check if user is authorized to view this report
        $this->authorize('view', $callReport);

        // Eager-load the relationships for the single response
        $callReport->load(['customer', 'company', 'serviceTypes']); // <-- ADD 'serviceTypes'

        return new CallReportResource($callReport);
    }
}
