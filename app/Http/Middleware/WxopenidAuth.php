<?php

namespace App\Http\Middleware;

use Closure;

class WxopenidAuth
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
        // 必须通过微信已经登录才能获得权限
        if (!\Session::get('agent')['agent_openid']) {
            return \Redirect()->route('wxlogin');
        }
        return $next($request);
    }
}
