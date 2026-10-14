<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

use App\Models\User;

use App\Models\Role;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Hash;





class UserManagementController extends Controller
{



    /**
     * Users List
     */
    public function index()
    {


        $users = User::with('roles')

            ->orderByDesc('id')

            ->paginate(15);



        return view(

            'admin.users.index',

            compact('users')

        );


    }









    /**
     * Create User
     */
    public function create()
    {


        $roles = Role::orderBy('name')

            ->get();



        return view(

            'admin.users.create',

            compact('roles')

        );


    }











    /**
     * Store User
     */
    public function store(Request $request)
    {



        $data = $request->validate([



            'name' => [

                'required',

                'string',

                'max:120',

            ],





            'email' => [

                'required',

                'email',

                'unique:users,email',

            ],





            'password' => [

                'required',

                'string',

                'min:8',

            ],





            'role_id' => [

                'nullable',

                'exists:roles,id',

            ],





            'status' => [

                'required',

                'in:active,inactive',

            ],



        ]);








        DB::transaction(function () use ($data) {



            $user = User::create([



                'name' => $data['name'],



                'email' => $data['email'],



                'password' => Hash::make(

                    $data['password']

                ),



                'status' => $data['status'],



            ]);






            if(!empty($data['role_id'])){


                $user->roles()->attach(

                    $data['role_id']

                );


            }




        });







        return redirect()

            ->route('admin.users.index')

            ->with(

                'success',

                __('users.create_success')

            );



    }












    /**
     * Show User
     */
    public function show(User $user)
    {


        $user->load('roles');



        return view(

            'admin.users.show',

            compact('user')

        );


    }












    /**
     * Edit User
     */
    public function edit(User $user)
    {


        $roles = Role::orderBy('name')

            ->get();




        $user->load('roles');





        return view(

            'admin.users.edit',

            compact(

                'user',

                'roles'

            )

        );


    }












    /**
     * Update User
     */
    public function update(Request $request, User $user)
    {



        $data = $request->validate([



            'name' => [

                'required',

                'string',

                'max:120',

            ],





            'email' => [

                'required',

                'email',

                'unique:users,email,'.$user->id,

            ],





            'password' => [

                'nullable',

                'string',

                'min:8',

            ],





            'role_id' => [

                'nullable',

                'exists:roles,id',

            ],





            'status' => [

                'required',

                'in:active,inactive',

            ],



        ]);









        DB::transaction(function () use ($user,$data) {



            $updateData = [



                'name' => $data['name'],



                'email' => $data['email'],



                'status' => $data['status'],



            ];








            if(!empty($data['password'])){


                $updateData['password'] = Hash::make(

                    $data['password']

                );


            }






            $user->update($updateData);








            if(isset($data['role_id'])){


                $user->roles()->sync([

                    $data['role_id']

                ]);


            }

            else{


                $user->roles()->detach();


            }





        });









        return redirect()

            ->route('admin.users.index')

            ->with(

                'success',

                __('users.update_success')

            );



    }












    /**
     * Delete User
     */
    public function destroy(User $user)
    {



        if(auth()->id() == $user->id){


            return back()

                ->with(

                    'error',

                    __('users.cannot_delete_current')

                );


        }








        DB::transaction(function () use ($user){



            $user->roles()

                ->detach();




            $user->delete();




        });









        return redirect()

            ->route('admin.users.index')

            ->with(

                'success',

                __('users.delete_success')

            );



    }



}