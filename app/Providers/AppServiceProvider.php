<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 数据库191字节
        Schema::defaultStringLength(191);
        // 人性化时间
        Carbon::setLocale('zh');

         // 视图Composer共享数据-前台，传递给agent模板
         view()->composer('agent.*', function($view) {
            // 因为需要用到版本管理，所以把controller控制器传递过去
            $controller = new Controller;        
            // 赋值
            $view->with(compact('controller'));
        });

         // 视图Composer共享数据-前台，传递给test模板
         view()->composer('test.*', function($view) {
            // 因为需要用到版本管理，所以把controller控制器传递过去
            $controller = new Controller;
            // 赋值
            $view->with(compact('controller'));
        });

        // 扩展身份证验证规则
        // 身份证
        Validator::extend('identitycards', function($attribute, $value, $parameters) {
            return preg_match('/(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}$)/', $value);
        });

        // 手机号
        Validator::extend('telphone', function($attribute, $value, $parameters) {
            return preg_match('/^1[34578][0-9]{9}$/', $value);
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
