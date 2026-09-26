<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (! $user) {
            return redirect()
                ->route('admin.login')
                ->with('error', 'Please login to access the admin panel.');
        }

        if (
            ! $user->isOwner()
            && ! $user->hasPermission('staff.view')
        ) {
            abort(403, 'You do not have permission to access the admin panel.');
        }

        return $next($request);
    }
}
