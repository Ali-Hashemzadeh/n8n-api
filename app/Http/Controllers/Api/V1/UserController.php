<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Add authorization middleware to the controller.
     */
    public function __construct()
    {
        // Use your new, granular permissions
        $this->middleware('can:see-users')->only(['index', 'show']);

        $this->middleware('can:manage-users')->only([
            'store', 'update', 'destroy'
        ]);
    }

    /**
     * Display a listing of the users.
     * @OA\Get(
     * path="/api/v1/users",
     * summary="Get list of users",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\Response(response=200, description="List of users"),
     * @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index()
    {
        // Use pagination and eager load roles
        $users = User::with('roles')->paginate(15);

        return UserResource::collection($users);
    }

    /**
     * Store a newly created user in storage.
     * @OA\Post(
     * path="/api/v1/users",
     * summary="Create a new user (and assign role/permissions)",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"name", "email", "password", "password_confirmation", "role"},
     * @OA\Property(property="name", type="string", example="New User"),
     * @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     * @OA\Property(property="mobile", type="string", example="09123456789"),
     * @OA\Property(property="password", type="string", format="password", example="password123"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     * @OA\Property(property="role", type="string", example="Observer"),
     * @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"see-users"})
     * )
     * ),
     * @OA\Response(response=201, description="User created successfully"),
     * @OA\Response(response=403, description="Forbidden"),
     * @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'mobile' => ['required', 'string', 'unique:'.User::class], // Make sure this matches your DB
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            // Validation for roles and permissions
            'role' => ['required', 'string', 'exists:roles,name'],
            'permissions' => ['sometimes', 'array'], // 'sometimes' means it's optional
            'permissions.*' => ['string', 'exists:permissions,name'] // Validate each item in the array
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
        ]);

        // Assign the role
        $user->assignRole($request->role);

        // Assign any direct permissions (if provided)
        if ($request->has('permissions')) {
            $user->givePermissionTo($request->permissions);
        }

        $user->load('roles', 'permissions'); // Load both for the response

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified user.
     * @OA\Get(
     * path="/api/v1/users/{id}",
     * summary="Get a single user",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="User details"),
     * @OA\Response(response=404, description="User not found"),
     * @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function show(User $user)
    {
        // Eager load roles and all permissions (both direct and via roles)
        $user->load('roles', 'permissions');

        return new UserResource($user);
    }

    /**
     * Update the specified user in storage.
     * @OA\Put(
     * path="/api/v1/users/{id}",
     * summary="Update a user, their role, and permissions",
     * tags={"Users"},
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * @OA\JsonContent(
     * @OA\Property(property="name", type="string", example="Updated User"),
     * @OA\Property(property="role", type="string", example="Observer"),
     * @OA\Property(property="permissions", type="array", @OA\Items(type="string"), example={"see-users"})
     * )
     * ),
     * @OA\Response(response=200, description="User updated"),
     * @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            // Add email/mobile validation if you want to allow updating them (make sure to add 'unique' rule)
            'role' => ['sometimes', 'string', 'exists:roles,name'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name']
        ]);

        if ($request->has('name')) {
            $user->update(['name' => $request->name]);
        }

        // Use sync methods for updates. This REPLACES all old roles/permissions
        // with the new set.

        if ($request->has('role')) {
            $user->syncRoles([$request->role]);
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        $user->load('roles', 'permissions');

        return new UserResource($user);
    }

    /**
     * Remove the specified user from storage.
     * (You can implement this logic)
     */
    public function destroy(User $user)
    {
        // Add logic to delete the user
        // $user->delete();
        // return response()->json(null, 204);
        return response()->json(['message' => 'Destroy method not yet implemented.'], 501);
    }
}
