<?php

namespace App\Http\Middleware;

use Closure;

class ManagerAuth
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
        if (empty($request->session()->get('admin'))) {
            return redirect('/admin/login');
        }
        return $next($request);
    }
}
