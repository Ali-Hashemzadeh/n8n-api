<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="CustomerResource",
 * title="CustomerResource",
 * description="Customer resource model",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="phone", type="string", example="555-123-4567"),
 * @OA\Property(property="name", type="string", example="Jane Doe"),
 * @OA\Property(property="email", type="string", format="email", example="jane.doe@example.com")
 * )
 */
class CustomerResource extends JsonResource
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
            'phone' => $this->phone,
            'name' => trim($this->name . ' ' . $this->lastname), // Combine name fields
            'email' => $this->email,
        ];
    }
}
