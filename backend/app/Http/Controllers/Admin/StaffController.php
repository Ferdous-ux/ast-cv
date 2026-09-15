<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffRequest;
use App\Http\Requests\Admin\UpdateStaffRequest;
use App\Http\Requests\Admin\UpdateStaffRolesRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort_unless(
            $request->user()->can('viewAny', User::class),
            403
        );

        $staff = User::query()
            ->with([
                'roles:id,name,slug',
            ])
            ->orderBy('id')
            ->paginate(
                min(
                    max((int) $request->input('per_page', 15), 1),
                    100
                )
            );

        return response()->json([
            'data' => $staff,
        ]);
    }

    public function show(
        Request $request,
        User $staff
    ): JsonResponse {
        abort_unless(
            $request->user()->can('view', $staff),
            403
        );

        $staff->load([
            'roles.permissions',
        ]);

        return response()->json([
            'data' => $staff,
        ]);
    }

    public function store(
        StoreStaffRequest $request
    ): JsonResponse {
        $data = $request->validated();

        $staff = DB::transaction(function () use ($data) {
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status' => $data['status'] ?? 'active',
            ]);
        });

        $staff->load('roles:id,name,slug');

        return response()->json([
            'message' => 'Staff member created successfully.',
            'data' => $staff,
        ], 201);
    }

    public function update(
        UpdateStaffRequest $request,
        User $staff
    ): JsonResponse {
        $data = $request->validated();

        $staff->update($data);

        $staff->load('roles:id,name,slug');

        return response()->json([
            'message' => 'Staff member updated successfully.',
            'data' => $staff,
        ]);
    }

    public function destroy(
        Request $request,
        User $staff
    ): JsonResponse {
        abort_unless(
            $request->user()->can('delete', $staff),
            403
        );

        $staff->delete();

        return response()->json([
            'message' => 'Staff member deleted successfully.',
        ]);
    }

    public function updateRoles(
        UpdateStaffRolesRequest $request,
        User $staff
    ): JsonResponse {
        $data = $request->validated();

        $roles = Role::query()
            ->whereIn('slug', $data['roles'])
            ->get();

        if (
            $roles->count() !== count(array_unique($data['roles']))
        ) {
            return response()->json([
                'message' => 'One or more roles are invalid.',
            ], 422);
        }

        if (
            $roles->contains(
                fn (Role $role) => $role->slug === 'owner'
            )
            && ! $request->user()->isOwner()
        ) {
            return response()->json([
                'message' => 'Only the owner can assign the owner role.',
            ], 403);
        }

        DB::transaction(function () use ($staff, $roles) {
            $staff->roles()->sync(
                $roles->pluck('id')->all()
            );
        });

        $staff->load('roles:id,name,slug');

        return response()->json([
            'message' => 'Staff roles updated successfully.',
            'data' => $staff,
        ]);
    }
}