<?php

namespace App\Http\Middleware;

use App\Models\DashUser;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SetBranchSession
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
        /** @var DashUser */
        $user = Auth::user();
        if ($user)
            Session::put("branch", $user->getBranchValue());
        return $next($request);
    }
}
