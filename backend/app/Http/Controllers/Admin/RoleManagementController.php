<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class RoleManagementController extends Controller
{


    /**
     * Roles List
     */
    public function index()
    {


        $roles = Role::query()

            ->withCount('permissions')

            ->withCount('users')

            ->latest()

            ->paginate(15);





        return view(
            'admin.roles.index',
            compact('roles')
        );


    }









    /**
     * Edit Role Permissions
     */
    public function edit(Role $role)
    {


        $permissions = Permission::query()

            ->orderBy('group')

            ->orderBy('name')

            ->get()

            ->groupBy('group');






        $role->load('permissions');






        return view(
            'admin.roles.edit',
            compact(
                'role',
                'permissions'
            )
        );


    }









    /**
     * Update Role Permissions
     */
    public function update(
        Request $request,
        Role $role
    )
    {


        $data = $request->validate([


            'permissions' => [

                'nullable',

                'array',

            ],





            'permissions.*' => [

                'exists:permissions,id',

            ],


        ]);









        DB::transaction(function () use (
            $role,
            $data
        ) {



            $role->permissions()->sync(

                $data['permissions'] ?? []

            );



        });









        return redirect()

            ->route(
                'admin.roles.index'
            )

            ->with([

                'success' =>
                    __('roles.update_success'),

            ]);



    }



}