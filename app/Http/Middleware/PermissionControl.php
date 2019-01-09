<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\Manager;
use App\Model\Role;
use App\Model\Permission;

class PermissionControl
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
        // 登录用户信息
        $session = $request->session();
        $admin_id = $session->get('admin')['admin_id']; // 登录用户ID
        // 传过来的路径
        $path = $request->path();   // 当前访问的路径， admin/agent

        // 去掉前面的admin路径，只保留后面的
        $path = substr($path, 6);
        // 判断是否有控制器后面的/
        $controller_pos = strpos($path, '/');
        if ($controller_pos !== false) {
            // 取出控制器
            $controller = substr($path, 0, $controller_pos);
            // 取出方法
            $action = substr($path, $controller_pos+1);
        } else {
            $controller = $path;
            $action = '';
        }

        // 判断当前用户是否有访问controller/*的权限，如果有，则当前控制器完全放行
        // 获取controller/*的权限
        $permission_all = Permission::where('name', $controller . '/*')->first();

        // 当前用户的权限列表
        $myPermissions = Manager::find($admin_id);

        // 判断当前用户是否有访问*权限，如果有就放行
        if ($myPermissions->hasPermission($permission_all)) {
            // echo '您有该模块下所有访问权限';
            return $next($request);
        }
        
        // 判断当前用户是否有访问当前路由的权限
        // 我们规定，如果当前路由没有定义，那么就没有访问权限
        $permission_current = Permission::where('name', $path)->first();

        // echo '<pre>';
        // print_r($permission_current);
        // exit();

        if (empty($permission_current)) {
            return response()->view('errors.403');
        } else {
            // 如果系统存在这个权限，那么就判断是否拥有该权限
            if ($myPermissions->hasPermission($permission_current)) {
                return $next($request);
            } else {
                return response()->view('errors.403');
            }
        }

    }
}
