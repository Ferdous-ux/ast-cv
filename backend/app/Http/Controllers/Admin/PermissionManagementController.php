<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



class PermissionManagementController extends Controller
{


    /**
     * Permissions List
     */
    public function index()
    {


        $permissions = Permission::query()

            ->orderBy('name')

            ->paginate(15);



        return view(
            'admin.permissions.index',
            compact('permissions')
        );


    }









    /**
     * Store Permission
     */
    public function store(Request $request)
    {


        $data = $request->validate([


            'name' => [

                'required',

                'string',

                'max:120',

                'unique:permissions,name',

            ],


        ]);







        DB::transaction(function () use ($data) {



            $slug = Str::slug(
                $data['name'],
                '.'
            );



            // التأكد من عدم تكرار slug

            $counter = 1;

            $originalSlug = $slug;



            while (
                Permission::where('slug', $slug)->exists()
            ) {

                $slug = $originalSlug . '.' . $counter;

                $counter++;

            }






            Permission::create([


                'name' => $data['name'],


                'slug' => $slug,


                'guard_name' => 'admin',


            ]);



        });








        return redirect()

            ->route('admin.permissions.index')

            ->with([

                'success' =>

                    __('permissions.create_success'),

            ]);



    }









    /**
     * Delete Permission
     */
    public function destroy(Permission $permission)
    {



        DB::transaction(function () use ($permission) {



            if (method_exists($permission, 'roles')) {


                $permission->roles()

                    ->detach();


            }






            $permission->delete();



        });









        return redirect()

            ->route('admin.permissions.index')

            ->with([

                'success' =>

                    __('permissions.delete_success'),

            ]);



    }



}