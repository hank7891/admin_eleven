<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MemberIsLogin
{
    /**
     * 已登入者才可存取（登入頁完成前先導向註冊頁）
     */
    public function handle(Request $request, Closure $next)
    {
        if (empty(session(MEMBER_AUTH_SESSION))) {
            return redirect('member/register');
        }

        return $next($request);
    }
}

