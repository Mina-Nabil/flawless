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
        if (!Auth::user()->isOwner()) {
            if (request()->is('dash/*')) {
                return abort(404);
            } 
            if (request()->is('patients/*')) {
                return abort(404);
            } 
            if (request()->is('cash/*')) {
                return abort(404);
            } 
            if (request()->is('attendance/*')) {
                return abort(404);
            } 
            if (request()->is('visa/*')) {
                return abort(404);
            } 
            if (request()->is('feedbacks/*')) {
                return abort(404);
            } 
            if (request()->is('followups/*')) {
                return abort(404);
            } 
            if (request()->is('*/pricelist/*')) {
                return abort(404);
            } 
            if (request()->is('pricelist/*')) {
                return abort(404);
            } 
            if (request()->is('*/area')) {
                return abort(404);
            } 
            if (request()->is('area/*')) {
                return abort(404);
            } 
            if (request()->is('*/device')) {
                return abort(404);
            } 
            if (request()->is('device/*')) {
                return abort(404);
            } 
            if (request()->is('settings/*')) {
                return abort(404);
            } 
        }
        return $next($request);
    }
}
