<?php


namespace App\Http\Middleware;

use Closure;

class COORS
{


    public function handle($request, Closure $next, $guard = null)
    {
        if ($request->method() == 'OPTIONS') {
            return response('', 200);
        }
        return $next($request);
    }
}