<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;


class ActivityLogController extends Controller
{


    /**
     * Activity Logs List
     */
    public function index()
    {


        $activities = ActivityLog::query()


            ->with('user:id,name')


            ->latest()


            ->paginate(20);




        return view(
            'admin.activity-logs.index',
            compact('activities')
        );


    }



}