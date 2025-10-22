<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of all available permissions.
     * @OA\Get(
     * path="/api/v1/permissions",
     * summary="Get list of all permissions",
     * tags={"Roles & Permissions"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="List of permissions"),
     * @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index()
    {
        // We will just return the names
        return response()->json(Permission::all()->pluck('name'));
    }
}
