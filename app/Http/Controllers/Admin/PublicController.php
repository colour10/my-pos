<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Agent;
use App\Model\Manager;
// Redis一定要设置全路径，不能写成use Redis，以免命名冲突
use Illuminate\Support\Facades\Redis;
use Session;
// 验证码
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

class PublicController extends Controller
{
    // 登录
    public function login(Request $request)
    {
        // 判断如果已经登录，那么就跳转到后台首页
        if ($request->session()->has('admin')) {
            return redirect()->route('index');
        }

        // 渲染
        $page_title = '后台登录';
        $load = '';
        if ($this->is_weixin()) {
            // $load = 'agent.wx';
            $load = 'admin.public.login';
        } else {
            $load = 'admin.public.login';
        }
        return view($load, compact('page_title'));
    }

    // 登录逻辑
    public function logindo(Request $request)
    {
        // 验证
        $this->validate(request(), [
            'mobile' => 'regex:/^1[34578][0-9]{9}$/',
            'password' => 'required',
            'is_remember' => 'integer',
            'identity' => 'required|string',
        ]);

        // 逻辑
        $user = request(['mobile', 'password']);
        $is_remember = request('is_remember');
        $identity = request('identity');
        // 获取客户端ip地址
        $ip = $this->getIP();

        // 如果启用了验证码，那么就进行验证
        if (Redis::get($ip) > 2) {
            $yzm = request('txtCode2');
            if (Session::get('pccode') != $yzm) {
                // 如果存在，则加1，如果不存在，则初始化为1
                if (Redis::exists($ip)) {
                    Redis::incr($ip);
                } else {
                    Redis::set($ip, 1);
                }
                $response = [
                    'code' => '1',
                    'msg' => '验证码输入错误',
                    'corrent_code' => Session::get('pccode'),
                    'login_count' => Redis::get($ip),
                ];
                // 返回结果
                return $response;
            }
        }

        // 渲染
        switch ($identity) {
            case "agent":
                // 如果是合伙人登录
                if (\Auth::guard('agent')->attempt($user, $is_remember)) {

                    // 从managers数据表中取出当前用户的ID
                    $agent = Agent::select(['id', 'name'])->where('mobile', $user['mobile'])->first();

                    // 写入session
                    $request->session()->put('agent', [
                        'agent_mobile' => $user['mobile'],
                        'agent_id' => $agent['id'],
                        'agent_name' => $agent['name'],
                    ]);

                    // 渲染
                    return redirect()->route('AgentauthIndex');
                } else {
                    return redirect()->route('login');
                }
                break;
            case "admin":
                // 如果是admin登录
                if (\Auth::guard('admin')->attempt($user, $is_remember)) {
                    // 从managers数据表中取出当前用户的ID
                    $admin = Manager::select(['id', 'name'])->where('mobile', $user['mobile'])->first();
                    // 写入session
                    $request->session()->put('admin', [
                        'admin_mobile' => $user['mobile'],
                        'admin_id' => $admin['id'],
                        'admin_name' => $admin['name'],
                    ]);

                    // 写入最后登录时间
                    $date = date('Y-m-d H:i:s', time());
                    $admin->update(['last_login_at' => $date]);

                    // 因为已经登录成功，所以无需记录错误登录次数了，删除即可。
                    if (Redis::exists($ip)) {
                        Redis::del($ip);
                    }

                    // 渲染
                    $response = [
                        'code' => '0',
                        'msg' => '登录成功',
                    ];
                    return $response;
                    // return redirect()->route('index');
                } else {
                    // 如果登录失败，那么就记录其失败次数，超过3次，就出现验证码
                    // 如果存在，则加1，如果不存在，则初始化为1
                    if (Redis::exists($ip)) {
                        Redis::incr($ip);
                    } else {
                        Redis::set($ip, 1);
                    }
                    // 如果出错了，没必要重新渲染了，返回错误即可
                    $response = [
                        'code' => '1',
                        'msg' => '用户名或密码不正确',
                        'login_ip' => $ip,
                        'login_count' => Redis::get($ip),
                    ];
                    return $response;
                    // return back()->withErrors(['用户名或密码不正确']);
                }
                break;
            default:
                return view('errors.500');
        }

    }

    // 退出
    public function logout(Request $request)
    {
        \Auth::guard('admin')->logout();
        // 如果redis中存在此session，那么就删除
        if ($request->session()->has('admin')) {
            $request->session()->forget('admin');
        }
        return redirect()->route('login');
    }


    /**
     * 生成PC端验证码
     */
    public function captcha($tmp)
    {
        // $phrase = new PhraseBuilder;
        $phrase = new PhraseBuilder(4, '0123456789');
        // 设置验证码位数
        $code = $phrase->build(4);
        // 生成验证码图片的Builder对象，配置相应属性
        $builder = new CaptchaBuilder($code, $phrase);
        // 设置背景颜色
        $builder->setBackgroundColor(123, 203, 230);
        $builder->setMaxAngle(25);
        $builder->setMaxBehindLines(0);
        $builder->setMaxFrontLines(0);
        // 可以设置图片宽高及字体
        $builder->build($width = 90, $height = 35, $font = null);
        // 获取验证码的内容
        $phrase = $builder->getPhrase();
        // 把内容存入session
        Session::put('pccode', $phrase);
        // 生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-Type:image/png");
        $builder->output();
    }

    /**
     * 取出当前系统保存的验证码(PC端)
     */
    public function getcaptcha()
    {
        return Session::get('pccode');
    }

}
