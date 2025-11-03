<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="UserResource",
 * title="UserResource",
 * description="User resource model",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="Ali Melmedas"),
 * @OA\Property(property="email", type="string", format="email", example="ali.melmedas1383@gmail.com"),
 * @OA\Property(property="mobile", type="string", example="09197238119"),
 * @OA\Property(
 * property="company",
 * type="object",
 * nullable=true,
 * example={"id": 1, "name": "MELMEDAS.co"},
 * @OA\Property(property="id", type="integer"),
 * @OA\Property(property="name", type="string")
 * ),
 * @OA\Property(
 * property="roles",
 * type="array",
 * @OA\Items(type="string"),
 * example={"Super-Admin"}
 * ),
 * @OA\Property(
 * property="permissions",
 * type="array",
 * @OA\Items(type="string"),
 * example={"manage-users", "see-users"}
 * ),
 * @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-03T12:00:00.000000Z"),
 * @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-03T12:00:00.000000Z")
 * )
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // --- Basic User Details ---
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,

            'company' => $this->whenLoaded('company', function () {
                // $this->company will be null if company_id was null
                if ($this->company) {
                    return [
                        'id' => $this->company->id,
                        'name' => $this->company->name,
                    ];
                }
                return 'mamad'; // Return null if no company is associated
            }),

            // --- Roles & Permissions ---

            // This will include the list of role names (e.g., ["Super-Admin"])
            // It will only be included if you've loaded them with $user->load('roles')
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('name');
            }),

            // This will include direct permissions (e.g., ["see-users"])
            // It will only be included if you've loaded them with $user->load('permissions')
            'permissions' => $this->whenLoaded('permissions', function () {
                return $this->permissions->pluck('name');
            }),

            // --- Timestamps ---
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
