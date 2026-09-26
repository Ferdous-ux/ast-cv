<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Resume;
use App\Models\Language;
use App\Models\ActivityLog;


class DashboardController extends Controller
{

    public function index()
    {


        /*
        |--------------------------------------------------------------------------
        | Dashboard Statistics
        |--------------------------------------------------------------------------
        */


        $stats = [


            [
                'title' => 'users',

                'value' => User::count(),

                'growth' => '+12.4%',

                'icon' => 'U',
            ],




            [
                'title' => 'resumes',

                'value' => Resume::count(),

                'growth' => '+8.2%',

                'icon' => 'R',
            ],




            [
                'title' => 'languages',

                'value' => Language::where(
                    'is_active',
                    true
                )->count(),

                'growth' => 'active',

                'icon' => 'L',
            ],




            [
                'title' => 'staff',

                'value' =>
                    User::whereHas('roles')->count(),

                'growth' => 'active',

                'icon' => 'S',
            ],



        ];









        /*
        |--------------------------------------------------------------------------
        | Recent Activity From Activity Logs
        |--------------------------------------------------------------------------
        */


        $activities = ActivityLog::query()


            ->with('user:id,name')


            ->latest()


            ->limit(5)


            ->get()

            ->map(function ($activity) {


                return [


                    'title' =>
                        $activity->description,



                    'user' =>
                        $activity->user?->name
                        ??
                        'System',




                    'action' =>
                        $activity->action,



                    'module' =>
                        $activity->module,



                    'time' =>
                        $activity->created_at
                            ->diffForHumans(),



                ];


            });












        /*
        |--------------------------------------------------------------------------
        | Platform Activity Chart
        |--------------------------------------------------------------------------
        */


        $chart = [


            'labels' => [],


            'users' => [],


            'resumes' => [],


            'total' => [],


        ];









        for ($i = 6; $i >= 0; $i--) {


            $date = Carbon::today()
                ->subDays($i);




            $chart['labels'][] =
                $date->format('d M');





            $dailyUsers = User::whereDate(
                    'created_at',
                    $date
                )
                ->count();





            $dailyResumes = Resume::whereDate(
                    'created_at',
                    $date
                )
                ->count();






            $chart['users'][] =
                $dailyUsers;




            $chart['resumes'][] =
                $dailyResumes;





            $chart['total'][] =
                $dailyUsers + $dailyResumes;


        }












        /*
        |--------------------------------------------------------------------------
        | Performance Overview
        |--------------------------------------------------------------------------
        */


        $totalResumes = Resume::count();



        $performance = [


            'ats' => $totalResumes > 0
                ? 100
                : 0,



            'skills' => $totalResumes > 0
                ? 100
                : 0,



            'experience' => $totalResumes > 0
                ? 100
                : 0,



        ];









        return view(

            'admin.dashboard.index',

            compact(

                'stats',

                'activities',

                'chart',

                'performance'

            )

        );


    }

}