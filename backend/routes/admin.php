<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StaffManagementController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Controllers\Admin\PermissionManagementController;
use App\Http\Controllers\Admin\UserManagementController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::prefix('admin')
    ->name('admin.')
    ->middleware('web')
    ->group(function () {





        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */


        Route::middleware('guest')->group(function () {


            Route::get('/login', [
                AuthController::class,
                'showLogin'
            ])
            ->name('login');





            Route::post('/login', [
                AuthController::class,
                'login'
            ])
            ->middleware('throttle:5,1')
            ->name('login.submit');


        });









        /*
        |--------------------------------------------------------------------------
        | Authenticated Admin
        |--------------------------------------------------------------------------
        */


        Route::middleware([
            'auth',
            'admin'
        ])
        ->group(function () {






            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */


            Route::get('/', [
                DashboardController::class,
                'index'
            ])
            ->name('dashboard');









            /*
            |--------------------------------------------------------------------------
            | Users Management
            |--------------------------------------------------------------------------
            */


            Route::prefix('users')
                ->name('users.')
                ->group(function () {



                    Route::get('/', [
                        UserManagementController::class,
                        'index'
                    ])
                    ->name('index');





                    Route::get('/create', [
                        UserManagementController::class,
                        'create'
                    ])
                    ->name('create');





                    Route::post('/', [
                        UserManagementController::class,
                        'store'
                    ])
                    ->name('store');





                    Route::get('/{user}/edit', [
                        UserManagementController::class,
                        'edit'
                    ])
                    ->name('edit');





                    Route::put('/{user}', [
                        UserManagementController::class,
                        'update'
                    ])
                    ->name('update');





                    Route::delete('/{user}', [
                        UserManagementController::class,
                        'destroy'
                    ])
                    ->name('destroy');



                });









            /*
            |--------------------------------------------------------------------------
            | Staff Management
            |--------------------------------------------------------------------------
            */


            Route::prefix('staff')
                ->name('staff.')
                ->group(function () {



                    Route::get('/', [
                        StaffManagementController::class,
                        'index'
                    ])
                    ->name('index');





                    Route::get('/create', [
                        StaffManagementController::class,
                        'create'
                    ])
                    ->name('create');





                    Route::post('/', [
                        StaffManagementController::class,
                        'store'
                    ])
                    ->name('store');





                    Route::get('/{staff}/edit', [
                        StaffManagementController::class,
                        'edit'
                    ])
                    ->name('edit');





                    Route::put('/{staff}', [
                        StaffManagementController::class,
                        'update'
                    ])
                    ->name('update');





                    Route::delete('/{staff}', [
                        StaffManagementController::class,
                        'destroy'
                    ])
                    ->name('destroy');



                });









            /*
            |--------------------------------------------------------------------------
            | Activity Logs
            |--------------------------------------------------------------------------
            */


            Route::get('/activity', [
                ActivityLogController::class,
                'index'
            ])
            ->name('activity.index');









            /*
            |--------------------------------------------------------------------------
            | Notifications
            |--------------------------------------------------------------------------
            */


            Route::prefix('notifications')
                ->name('notifications.')
                ->group(function () {



                    Route::get('/', [
                        NotificationController::class,
                        'index'
                    ])
                    ->name('index');





                    Route::post('/{id}/read', [
                        NotificationController::class,
                        'read'
                    ])
                    ->name('read');





                    Route::post('/read-all', [
                        NotificationController::class,
                        'readAll'
                    ])
                    ->name('read-all');





                    Route::delete('/{id}', [
                        NotificationController::class,
                        'destroy'
                    ])
                    ->name('destroy');





                    Route::delete('/', [
                        NotificationController::class,
                        'destroyAll'
                    ])
                    ->name('destroy-all');



                });









            /*
            |--------------------------------------------------------------------------
            | Roles Management
            |--------------------------------------------------------------------------
            */


            Route::prefix('roles')
                ->name('roles.')
                ->group(function () {



                    Route::get('/', [
                        RoleManagementController::class,
                        'index'
                    ])
                    ->name('index');





                    Route::get('/{role}/edit', [
                        RoleManagementController::class,
                        'edit'
                    ])
                    ->name('edit');





                    Route::put('/{role}', [
                        RoleManagementController::class,
                        'update'
                    ])
                    ->name('update');



                });









            /*
            |--------------------------------------------------------------------------
            | Permissions Management
            |--------------------------------------------------------------------------
            */


            Route::prefix('permissions')
                ->name('permissions.')
                ->group(function () {



                    Route::get('/', [
                        PermissionManagementController::class,
                        'index'
                    ])
                    ->name('index');





                    Route::post('/', [
                        PermissionManagementController::class,
                        'store'
                    ])
                    ->name('store');





                    Route::delete('/{permission}', [
                        PermissionManagementController::class,
                        'destroy'
                    ])
                    ->name('destroy');



                });









            /*
            |--------------------------------------------------------------------------
            | Localization
            |--------------------------------------------------------------------------
            */


            Route::post('/locale', function (Request $request) {



                $locale = $request->validate([

                    'locale' => [

                        'required',

                        'in:ar,en'

                    ],


                ])['locale'];





                session([

                    'locale' => $locale,

                ]);





                return back();



            })
            ->name('locale');









            /*
            |--------------------------------------------------------------------------
            | Logout
            |--------------------------------------------------------------------------
            */


            Route::post('/logout', [
                AuthController::class,
                'logout'
            ])
            ->name('logout');






        });



    });