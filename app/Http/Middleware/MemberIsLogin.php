<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MemberIsLogin
{
    /**
     * 已登入者才可存取（未登入導向會員登入頁）
     */
    public function handle(Request $request, Closure $next)
    {
        if (empty(session(MEMBER_AUTH_SESSION))) {
            return redirect('member/login');
        }

        return $next($request);
    }
}

