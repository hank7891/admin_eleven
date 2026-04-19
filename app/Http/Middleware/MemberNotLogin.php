<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MemberNotLogin
{
    /**
     * 未登入者才可存取
     */
    public function handle(Request $request, Closure $next)
    {
        if (!empty(session(MEMBER_AUTH_SESSION))) {
            return redirect('/');
        }

        return $next($request);
    }
}

