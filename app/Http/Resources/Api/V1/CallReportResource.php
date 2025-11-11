<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="CallReportResource",
 * title="CallReportResource",
 * description="Call Report resource model",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="summary", type="string", example="Customer confirmed appointment..."),
 *
 * @OA\Property(
 * property="conversation",
 * type="object",
 * description="The call transcript and other JSON data.",
 * @OA\Property(
 * property="transcript",
 * type="array",
 * @OA\Items(
 * type="object",
 * @OA\Property(property="speaker", type="string", example="AI"),
 * @OA\Property(property="text", type="string", example="Hello, how can I help you?")
 * )
 * )
 * ),
 *
 * @OA\Property(
 * property="metadata",
 * type="object",
 * description="Flexible metadata about the call.",
 * @OA\Property(property="duration_seconds", type="integer", example=124)
 * ),
 *
 * @OA\Property(property="state", type="string", example="confirmed"),
 * @OA\Property(property="called_at", type="string", format="date-time", example="2025-11-05T12:30:00Z"),
 * @OA\Property(property="company", ref="#/components/schemas/CompanyResource"),
 * @OA\Property(property="customer", ref="#/components/schemas/CustomerResource")
 * )
 */
class CallReportResource extends JsonResource
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
            'summary' => $this->summary,
            'conversation' => $this->conversation,
            'metadata' => $this->metadata,
            'state' => $this->state,
            'called_at' => $this->created_at->toIso8601String(), // Use created_at as the call time

            // --- Eager-loaded Relationships ---
            // This is the correct way to include company and customer data
            'company' => new CompanyResource($this->whenLoaded('company')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),

            // --- THIS IS THE FIX ---
            // Use ::collection() for a "many" relationship
            'service_types' => ServiceTypeResource::collection($this->whenLoaded('serviceTypes')),
        ];
    }
}
