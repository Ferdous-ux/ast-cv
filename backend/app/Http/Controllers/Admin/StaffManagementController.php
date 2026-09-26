<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStaffRequest;
use App\Http\Requests\Admin\UpdateStaffRequest;
use App\Models\Role;
use App\Models\User;
use App\Notifications\AdminActivityNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;



class StaffManagementController extends Controller
{


    /**
     * Staff List
     */
    public function index()
    {

        $staff = User::query()

            ->with([
                'roles:id,name,slug'
            ])

            ->whereHas('roles')

            ->latest()

            ->paginate(10);



        return view(
            'admin.staff.index',
            compact('staff')
        );

    }








    /**
     * Create Staff Page
     */
    public function create()
    {

        $roles = Role::select(
            'id',
            'name',
            'slug'
        )
        ->get();



        return view(
            'admin.staff.create',
            compact('roles')
        );

    }









    /**
     * Store Staff
     */
    public function store(
        StoreStaffRequest $request
    )
    {


        $data = $request->validated();



        $roles = $request->input(
            'roles',
            []
        );







        $staff = DB::transaction(function () use (
            $data,
            $roles
        ) {



            $user = User::create([


                'name' =>
                    $data['name'],


                'email' =>
                    $data['email'],


                'password' =>
                    Hash::make(
                        $data['password']
                    ),


                'status' =>
                    $data['status']
                    ??
                    'active',


            ]);






            if (!empty($roles)) {


                $user->roles()->sync(
                    $roles
                );


            }




            return $user;



        });








        $staff->load('roles');







        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */


        activity()->log(

            'created',

            'staff',

            'Created new staff member: '.$staff->name,

            $staff,

            [

                'email' =>
                    $staff->email,

                'status' =>
                    $staff->status,

            ]

        );








        /*
        |--------------------------------------------------------------------------
        | Notification
        |--------------------------------------------------------------------------
        */


        auth()->user()->notify(

            new AdminActivityNotification(

                'notifications.staff_created_title',

                [

                    'message' =>
                        'notifications.staff_created_message',

                    'name' =>
                        $staff->name,

                ]

            )

        );








        return redirect()

            ->route('admin.staff.index')

            ->with([

                'success' =>
                    __('messages.created_successfully'),

                'alert_type' =>
                    'success',

            ]);


    }













    /**
     * Edit Staff Page
     */
    public function edit(User $staff)
    {


        $roles = Role::select(
            'id',
            'name',
            'slug'
        )
        ->get();




        $staff->load('roles');





        return view(
            'admin.staff.edit',
            compact(
                'staff',
                'roles'
            )
        );


    }













    /**
     * Update Staff
     */
    public function update(
        UpdateStaffRequest $request,
        User $staff
    )
    {


        $data = $request->validated();



        $roles = $request->input(
            'roles',
            []
        );








        DB::transaction(function () use (
            $data,
            $roles,
            $staff
        ) {




            $staff->update([


                'name' =>
                    $data['name'],


                'email' =>
                    $data['email'],


                'status' =>
                    $data['status']
                    ??
                    $staff->status,


            ]);








            if (!empty($data['password'])) {


                $staff->update([


                    'password' =>
                        Hash::make(
                            $data['password']
                        )


                ]);


            }







            $staff->roles()->sync(
                $roles
            );




        });








        activity()->log(

            'updated',

            'staff',

            'Updated staff member: '.$staff->name,

            $staff,

            [

                'email' =>
                    $staff->email,

                'status' =>
                    $staff->status,

            ]

        );









        auth()->user()->notify(

            new AdminActivityNotification(

                'notifications.staff_updated_title',

                [

                    'message' =>
                        'notifications.staff_updated_message',

                    'name' =>
                        $staff->name,

                ]

            )

        );









        return redirect()

            ->route('admin.staff.index')

            ->with([

                'success' =>
                    __('messages.updated_successfully'),

                'alert_type' =>
                    'success',

            ]);



    }













    /**
     * Delete Staff
     */
    public function destroy(User $staff)
    {


        $staffName = $staff->name;





        DB::transaction(function () use ($staff) {


            $staff->roles()->detach();


            $staff->delete();


        });








        activity()->log(

            'deleted',

            'staff',

            'Deleted staff member: '.$staffName,

            null,

            [

                'name' =>
                    $staffName,

            ]

        );









        auth()->user()->notify(

            new AdminActivityNotification(

                'notifications.staff_deleted_title',

                [

                    'message' =>
                        'notifications.staff_deleted_message',

                    'name' =>
                        $staffName,

                ]

            )

        );









        return redirect()

            ->route('admin.staff.index')

            ->with([

                'success' =>
                    __('messages.deleted_successfully'),

                'alert_type' =>
                    'success',

            ]);



    }



}