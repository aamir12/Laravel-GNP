<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! Auth::user()->hasAnyRole('sa', 'admin')) {
            return response(['status' => 'error', 'message' => __('admin_can_access')], 403);
        }
        return $next($request);
    }
}