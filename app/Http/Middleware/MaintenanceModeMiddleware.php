<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\BusinessManagement\Entities\BusinessSetting;

class MaintenanceModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ((bool) BusinessSetting::where('key_name', 'maintenance_mode')->first()?->value ?? false)
        {
            return response()->json([
                'message' => "Server under maintenance"
            ],503);
        }
        return $next($request);
    }
}
