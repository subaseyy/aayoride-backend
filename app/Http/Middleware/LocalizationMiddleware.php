<?php

namespace App\Http\Middleware;

use App\Utils\Helpers;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle($request, Closure $next)
    {
//        $local = ($request->hasHeader('X-localization')) ? (strlen($request->header('X-localization'))>0?$request->header('X-localization'): 'en'): 'en';
        $local = ($request->hasHeader('X-localization')) ? (strlen($request->header('X-localization')) > 0 ? $request->header('X-localization') : defaultLang()) : defaultLang();
        App::setLocale($local);
        return $next($request);
    }
}
