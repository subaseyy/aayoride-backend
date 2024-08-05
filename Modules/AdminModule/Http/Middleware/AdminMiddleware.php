<?php

namespace Modules\AdminModule\Http\Middleware;

use Brian2694\Toastr\Facades\Toastr;
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && (auth()->user()->user_type == 'admin-employee' || auth()->user()->user_type == 'super-admin')) {
            return $next($request);
        }
        return redirect(route('admin.auth.login'));
    }
}
