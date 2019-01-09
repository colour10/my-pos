<?php

namespace App\Http\Middleware;

use Closure;

class WxAuth
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
        // 必须用微信访问，否则报错
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false) {
            return response()->view('errors.wxerror');
        }
        return $next($request);
    }
}
