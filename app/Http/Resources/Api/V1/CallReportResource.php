<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class CallReportResource extends JsonResource
{

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
            // This is the correct way to include customer data
            'company' => new CompanyResource($this->whenLoaded('company')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
        ];
    }
}
