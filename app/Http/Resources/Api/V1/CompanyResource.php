<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="CompanyResource",
 * title="CompanyResource",
 * description="Company resource model",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="MELMEDAS.co"),
 * @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-03T12:00:00.000000Z"),
 * @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-03T12:00:00.000000Z"),
 * @OA\Property(property="users", type="array", @OA\Items(ref="#/components/schemas/UserResource")),
 * @OA\Property(property="service_types", type="array", @OA\Items(ref="#/components/schemas/ServiceTypeResource"))
 * )
 */
class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),

            // --- Relationships ---
            // These will only be included if you explicitly
            // load them in your controller, e.g., $company->load('users')

            'users' => UserResource::collection(
                $this->whenLoaded('users')
            ),

            'service_types' => ServiceTypeResource::collection(
                $this->whenLoaded('serviceTypes')
            ),

            'customers' => CustomerResource::collection(
                $this->whenLoaded('customers')
            )
        ];
    }
}
