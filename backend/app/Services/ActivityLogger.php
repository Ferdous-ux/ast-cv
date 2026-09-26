<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;


class ActivityLogger
{


    public function log(
        string $action,
        ?string $module = null,
        ?string $description = null,
        ?Model $subject = null,
        array $properties = []
    ): ActivityLog
    {


        return ActivityLog::create([


            'user_id' => Auth::id(),


            'action' => $action,


            'module' => $module,



            'description' => $description,



            'subject_type' =>
                $subject
                ? get_class($subject)
                : null,



            'subject_id' =>
                $subject?->id,



            'ip_address' =>
                Request::ip(),



            'properties' =>
                $properties,


        ]);

    }


}