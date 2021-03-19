<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckType
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
        if (Auth::user()->DASH_TYPE_ID != 1) {
            if (request()->is('cash/*')) {
                return abort(404);
            } 
            if (request()->is('attendance/*')) {
                return abort(404);
            } 
        }
        return $next($request);
    }
}
