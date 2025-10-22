<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     * @OA\Get(
     * path="/api/v1/roles",
     * summary="Get list of roles",
     * tags={"Roles & Permissions"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="List of roles"),
     * @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index()
    {
        // Eager load the permissions count for each role
        return response()->json(Role::withCount('permissions')->get());
    }

    /**
     * Display the specified role and its permissions.
     * @OA\Get(
     * path="/api/v1/roles/{id}",
     * summary="Get a single role and its permissions",
     * tags={"Roles & Permissions"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="Role details")
     * )
     */
    public function show(Role $role)
    {
        // Eager load the permissions for this role
        $role->load('permissions');

        return response()->json([
            'role' => $role->name,
            'permissions' => $role->permissions->pluck('name')
        ]);
    }

    /**
     * Assign a permission to a role.
     * @OA\Post(
     * path="/api/v1/roles/{id}/permissions",
     * summary="Assign a permission to a role",
     * tags={"Roles & Permissions"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"permission_name"},
     * @OA\Property(property="permission_name", type="string", example="manage-posts")
     * )
     * ),
     * @OA\Response(response=200, description="Permission assigned")
     * )
     */
    public function assignPermission(Request $request, Role $role)
    {
        $request->validate([
            'permission_name' => 'required|string|exists:permissions,name'
        ]);

        $role->givePermissionTo($request->permission_name);
        $role->load('permissions'); // Reload permissions

        return response()->json([
            'message' => 'Permission assigned successfully.',
            'permissions' => $role->permissions->pluck('name')
        ]);
    }

    /**
     * Revoke (delete) a permission from a role.
     * @OA\Delete(
     * path="/api/v1/roles/{id}/permissions",
     * summary="Revoke a permission from a role",
     * tags={"Roles & Permissions"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"permission_name"},
     * @OA\Property(property="permission_name", type="string", example="manage-posts")
     * )
     * ),
     * @OA\Response(response=200, description="Permission revoked")
     * )
     */
    public function revokePermission(Request $request, Role $role)
    {
        $request->validate([
            'permission_name' => 'required|string|exists:permissions,name'
        ]);

        $permission = Permission::findByName($request->permission_name);

        if ($role->hasPermissionTo($permission)) {
            $role->revokePermissionTo($permission);
            $role->load('permissions'); // Reload permissions

            return response()->json([
                'message' => 'Permission revoked successfully.',
                'permissions' => $role->permissions->pluck('name')
            ]);
        }

        return response()->json(['message' => 'Role does not have this permission.'], 400);
    }
}
