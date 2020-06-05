<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Jobs\MakePayment;
use App\Models\AdvanceMethod;
use App\Models\Agent;
use App\Models\AgentAccount;
use App\Models\ApplyCard;
use App\Models\Bank;
use App\Models\Card;
use App\Models\Cardbox;
use App\Models\Finance;
use App\Models\WechatMessage;
use App\Models\Withdraw;
use BankCard;
use Cache;
use DB;
use EasyWeChat\Factory;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Hash;
use Illuminate\Http\Request;
use Log;
use Session;

class AgentauthController extends Controller
{
    // request对象
    protected $request;
    // redis变量
    protected $cache;
    // 配置参数
    protected $config;
    // 微信实例化
    protected $app;

    // 获取openid 的微信服务器地址
    const OPENIDURL = 'https://api.weixin.qq.com/sns/oauth2/access_token?';

    // 获取code 的微信服务器地址
    const CODEURL = "https://open.weixin.qq.com/connect/oauth2/authorize?";

    // SCOPE-只有openid
    const SCOPE = 'snsapi_base';

    // SCOPE-包含用户信息的全部内容
    const SCOPE_USER = 'snsapi_userinfo';

    /**
     * 构造函数
     */
    public function __construct(Request $request)
    {
        // request注入
        $this->request = $request;

        // 取出微信公众号配置参数
        $this->config = config('wechat.official_account.default');

        // 使用微信公众号配置参数来初始化一个公众号应用实例
        $this->app = Factory::officialAccount($this->config);
    }

    /**
     * 登录首页 [PC] 目前已经禁止访问，因为把微信端作为入口了
     */
    // public function index()
    // {
    //     // 逻辑
    //     $agent_id = Session::get('agent')['agent_id'];
    //     if (empty($agent_id)) {
    //         return redirect()->route('login');
    //     }
    //     $agent = Agent::findOrFail($agent_id);
    //     // 银行卡
    //     $cards = $agent->cards;
    //     foreach ($cards as $k => $card) {
    //         $cards[$k]->bankName = $card->bank->name;
    //         $cards[$k]->lastNumber = substr($card->card_number, -4);
    //     }

    //     // 结算通道
    //     $method = AdvanceMethod::where('status', '12222')->first();
    //     // 结算通道不能为空
    //     if (!$method) {
    //         exit('没有有效的结算通道，请检查数据库设置');
    //     }

    //     // 单张银行卡
    //     $card = $agent->card;
    //     if (empty($card)) {
    //         $card = Card::where('agent_id', $agent_id)->first();
    //     }

    //     // 渲染
    //     $page_title = '意远合伙人登录默认首页';
    //     return view('agent.index', compact('page_title', 'agent', 'method', 'cards', 'card'));
    // }

    /**
     * 退出登录，[微信端] 已经关闭了退出功能，作废
     */
    // public function logout()
    // {
    //     \Auth::guard('agent')->logout();
    //     // 如果redis中存在此session，那么就删除
    //     if ($this->request->session()->has('agent')) {
    //         $this->request->session()->forget('agent');
    //     }
    //     // 返回登录页面
    //     return redirect()->route('login');
    // }

    /**
     * 提现逻辑 [微信]
     */
    public function cash(Request $request)
    {
        // 验证
        $this->validate($request, [
            'card_id'       => 'required|integer',
            'sum'           => 'required|numeric|min:2',
            'cash_password' => 'required|digits:6',
            'openid'        => 'required',
        ]);

        // 逻辑
        $card_id = $request->card_id;

        // 判断合伙人
        $agentResult = $this->wxcheckbyopenid();
        if ($agentResult['code'] != '0') {
            $response = [
                'code' => $agentResult['code'],
                'msg'  => $agentResult['msg'],
            ];
            return $response;
        }
        // 取出合伙人
        $agent = $agentResult['data'];
        // 取出合伙人id
        $agent_id = $agent->id;

        // 接收到的提现密码
        $cash_password = $request->cash_password;

        // 首先判断提现密码正确与否
        if (!\Hash::check($cash_password, $agent->cash_password)) {
            $response = [
                'code' => '1',
                'msg'  => '提现密码不正确！',
            ];
            return $response;
        }

        $card = Card::findOrFail($card_id);
        $card_number = $card->card_number;
        $agentaccount = AgentAccount::where(['agent_id' => $agent_id])->first();
        // 用户输入的提现金额
        $sum = $request->sum;

        // 代付通道
        $method = $this->getAdvanceMethod();
        // 通道ID
        $method_id = $method->id;
        // 通道登录用户名
        $username = $method->username;
        // 通道登录密码
        $password = $method->password;
        // 通道商户代码
        $merchant_id = $method->merchant_id;
        // 通道业务类型
        $business_code = $method->business_code;
        // 通道限额
        $max = $method->max;
        // 通道每笔交易费用
        $charge = $method->per_charge;
        // 客户实际到账金额，涉及高精度运算
        $account = bcsub($sum, $charge, 2);
        // 默认为提现中
        $status = '0';
        // 交易流水号
        $agent_sid = $agent->sid;
        $agent_name = $agent->name;
        $cash_id = 'DF' . date('YmdHis') . mt_rand(1000, 9999);

        // 转账附言
        $remark = '合伙人' . $agent->sid . '于' . date('Y-m-d H:i:s', time()) . '申请提现' . $account . '元';

        // 用户余额，注意，这里涉及小数，需要用高精度进行运算
        $available_money = bcsub($agentaccount->available_money, $sum, 2);
        // $cash_money = bcadd($agentaccount->cash_money, $account, 2);
        // 把提现中余额改为原始的，不扣除手续费的
        $cash_money = bcadd($agentaccount->cash_money, $sum, 2);
        // 提现金额保持不变
        // $sum_money = bcsub($agentaccount->sum_money, $charge, 2);
        $sum_money = $agentaccount->sum_money;

        // 采用事务处理机制
        DB::beginTransaction();
        try {

            // 提现金额不能超过账户余额
            if ($sum > $agentaccount->available_money) {
                throw new \Exception('提现金额不能超过账户余额');
            }

            // 提现金额不能超出最大限额
            if ($account > $max) {
                throw new \Exception('提现金额不能超出最大限额');
            }

            // 提现金额不能少于2元
            if ($sum < $charge) {
                throw new \Exception('提现金额不能少于' . $charge . '元');
            }

            // 如果合伙人上一笔提现状态还未返回，那么就不能再次申请提现,也就是如果当前合伙人账户中有提现中的余额时，就不能再次发起提现
            // if ($agentaccount->cash_money > 0) {
            //     throw new \Exception('您的账户有提现中的分润，需要等待其处理完毕后才能继续~');
            // }

            // 创建提现新纪录
            // 开始创建
            if (!Withdraw::create(compact('cash_id', 'agent_id', 'method_id', 'sum', 'charge', 'account', 'status', 'card_id', 'remark'))) {
                throw new \Exception('提现数据表创建失败！');
            }

            // 然后更改用户账户表的余额，增加提现中余额(+$account)，可用余额(-$sum)，总金额(-$charge)
            if (!$agentaccount->update([
                'available_money' => $available_money,
                'cash_money'      => $cash_money,
                'sum_money'       => $sum_money,
            ])) {
                throw new \Exception('更新合伙人资产表失败！');
            }


            // 把result结果推送到队列，默认设置2分钟，基本上如果没有大的意外的话，二次确认也已经完成了,delay里面的参数填写的是秒数
            // 把最后的结果发过去即可，然后在任务队列中进行轮询。
            // $result,接口返回结果
            // $method,通道相关参数
            // $timeout,相隔多长时间再次重试，默认300秒
            // $attempt,最大重试次数
            // delay是延迟多少秒之后执行，默认5分钟，300秒
            // 把银行卡信息加入
            $withdraw = DB::table('withdraws')->where('cash_id', $cash_id)->first();
            $job = (new MakePayment($agent, $agentaccount, $card, $method, $withdraw, $timeout = 300, $attempt = 10))->delay(10);
            if (!$this->dispatch($job)) {
                throw new \Exception('推送提现任务到队列失败！');
            }

            // 提交
            DB::commit();

            // 结果返回
            $response = [
                'code' => '0',
                'msg'  => '您的提现已受理，请5分钟后查看到账情况~',
                'data' => [
                    'available_money' => $available_money,
                    'cash_money'      => $cash_money,
                ],
            ];
            return $response;

        } catch (\Exception $e) {
            DB::rollback();
            $response = [
                'code' => '1',
                'msg'  => $e->getMessage(),
            ];
            // 记录错误日志
            $msg = '';
            $msg .= '<pre>' . PHP_EOL;
            $msg .= '这里记录的是提现操作失败记录，主要内容如下：' . PHP_EOL;
            $msg .= '提现内容：' . $remark . PHP_EOL;
            $msg .= '提现错误：' . PHP_EOL;
            $arr = print_r($e->getMessage(), true);
            $msg .= "$arr";
            $msg .= PHP_EOL . PHP_EOL;
            // 写入日志
            Log::info($msg);
            // 返回
            return $response;
        }
    }




    /**
     * 微信-注册页面逻辑，已经关闭注册，废弃
     */
    // public function wxregdo(Request $request)
    // {
    //     // 验证
    //     $this->validate($request, [
    //         'mobile' => 'required|unique:agents,mobile|regex:/^1[345678][0-9]{9}$/',
    //         'password' => 'required',
    //     ]);

    //     // 逻辑
    //     $mobile = request('mobile');
    //     $password = bcrypt(request('password'));
    //     $method = request('method');
    //     // 判断用户和推广的来源openid是否为同一人，如果为同一人，那么就把parentopenid清空，说明推广无效。
    //     $wx_openid = request('wx_openid');
    //     $parentopenid = request('parentopenid');
    //     $openid = request('wx_openid');
    //     $wx_user = $this->getUserInfo();
    //     if ($wx_openid == $parentopenid) {
    //         $parentopenid = NULL;
    //     }

    //     // 新记录值
    //     // 因为要记录两次，所以这里启用事务处理
    //     DB::beginTransaction();
    //     try {

    //         // 新纪录值
    //         $agent = Agent::create(compact('mobile', 'password', 'parentopenid', 'method'));

    //         // 如果没有写入成功，那么就报错
    //         if (!$agent) {
    //             throw new \Exception('合伙人添加失败');
    //         }

    //         // 取出合伙人id
    //         $agent_id = $agent->id;

    //         // 写入sid值
    //         $result = \DB::table('agents')->select(\DB::raw("concat('M', right(concat('00000' , id), 5)) as sid"))->where('id', $agent_id)->get();
    //         $sid = $result[0]->sid;

    //         // 如果没有sid值，那么就报错
    //         if (!$sid) {
    //             throw new \Exception('生成合伙人编号失败');
    //         }
    //         // 更新合伙人编号
    //         if (!$agent->update(compact('sid'))) {
    //             throw new \Exception('写入合伙人编号失败');
    //         }

    //         // 如果agentaccount表没有这个用户，那么就新增
    //         if (!AgentAccount::firstOrCreate(['agent_id' => $agent_id], [
    //             'frozen_money' => '0.00',
    //             'available_money' => '0.00',
    //             'sum_money' => '0.00',
    //         ])) {
    //             throw new \Exception('写入合伙人资产表失败');
    //         }

    //         // 结算通道
    //         $method = AdvanceMethod::select(['per_charge'])->where('status', '1')->first();

    //         // 取出openid，然后和合伙人表进行绑定
    //         // 如果缓存不存在，那么就不写入
    //         // 数据库写入openid，进行绑定
    //         if (!$agent->update(compact('wx_openid', 'openid'))) {
    //             throw new \Exception('写入合伙人openid失败');
    //         }

    //         // 写入session
    //         $request->session()->put('agent', [
    //             'agent_mobile' => $mobile,
    //             'agent_id' => $agent_id,
    //             // 新注册还没有名字，用手机号代替
    //             'agent_name' => $mobile,
    //             'agent_openid' => $wx_openid,
    //             'agent_wxuser' => $wx_user,
    //         ]);

    //         // 提交
    //         DB::commit();

    //         // 成功返回
    //         $data = [
    //             'code' => '0',
    //             'msg' => '注册成功',
    //             'wx_user' => $wx_user,
    //             // 单笔提现手续费
    //             'per_charge' => $method->per_charge,
    //         ];
    //         return $data;

    //     } catch (\Exception $e) {
    //         // 回滚
    //         DB::rollback();
    //         $data = [
    //             'code' => '1',
    //             'msg' => $e->getMessage(),
    //         ];
    //         return $data;
    //     }

    // }


    /**
     * 微信-登录页面，废弃
     */
    // public function wxlogin()
    // {
    //     // // 如果已经登录，那么就跳转到“我的”页面
    //     // if (Session::get('agent')['agent_openid']) {
    //     //     return redirect()->route('wxmine');
    //     // } else {
    //     //     // 否则就进入登录页面
    //     //     // 把openid传入进去
    //     //     $wx_openid = $this->getOpenId();
    //     //     // 渲染
    //     //     return view('agent.wxlogin', compact('wx_openid'));
    //     // }

    //     // // 不用后台缓存判断了，只判断前台就好
    //     // // 存储openid
    //     // $wx_openid = $this->getOpenId();
    //     // // 渲染
    //     // return view('agent.wxlogin', compact('wx_openid'));

    //     // 看日志发现有的用户并不会提示授权，所以拿不到授权信息，这个时候就放弃使用授权，改用openid直接调用，直接用上面的方法
    //     // 获取授权用户信息
    //     // $user = $this->getauthuser();
    //     // // 渲染
    //     // return view('agent.wxlogin', compact('user'));
    //     // 注册页面不用授权了，显得啰嗦
    //     return view('agent.wxlogin');
    // }

    /**
     * 微信-登录逻辑 登录目前已经废弃
     */
    // public function wxlogindo(Request $request)
    // {
    //     // 验证
    //     $this->validate($request, [
    //         'mobile' => 'regex:/^1[34578][0-9]{9}$/',
    //         'password' => 'required',
    //         'wx_openid' => 'required',
    //     ]);

    //     // 逻辑
    //     // 首先获取到openid
    //     $wx_openid = request('wx_openid');
    //     $openid = request('wx_openid');
    //     $wx_user = $this->getUserInfo();
    //     $user = request(['mobile', 'password']);
    //     $is_remember = '1';
    //     // 获取客户端ip地址
    //     $ip = $this->getIP();
    //     // 如果启用了验证码，那么就进行验证
    //     if (Redis::get($ip) > 3) {
    //         $yzm = request('yzm');
    //         if (Session::get('wxcode') != $yzm) {
    //             // 如果存在，则加1，如果不存在，则初始化为1
    //             if (Redis::exists($ip)) {
    //                 Redis::incr($ip);
    //             } else {
    //                 Redis::set($ip, 1);
    //             }
    //             $response = [
    //                 'code' => '1',
    //                 'msg' => '验证码输入错误',
    //                 'login_ip' => $ip,
    //                 'login_count' => Redis::get($ip),
    //             ];
    //             // 返回结果
    //             return $response;
    //         }
    //     }

    //     if (\Auth::guard('agent')->attempt($user, $is_remember)) {

    //         // 从managers数据表中取出当前用户的ID
    //         $agent = Agent::where('mobile', $user['mobile'])->first();

    //         // 结算通道
    //         $method = AdvanceMethod::select(['per_charge'])->where('status', '1')->first();

    //         // 判断如果这个合伙人保存的openid和当前微信的openid不一致，那么禁止登录
    //         if ($agent->openid != $openid) {
    //             // 记录错误次数
    //             if (Redis::exists($ip)) {
    //                 Redis::incr($ip);
    //             } else {
    //                 Redis::set($ip, 1);
    //             }
    //             $response = [
    //                 'code' => '1',
    //                 'msg' => '当前微信号和合伙人手机不匹配，不能登录',
    //                 'login_ip' => $ip,
    //                 'login_count' => Redis::get($ip),
    //             ];
    //             return $response;
    //         }

    //         // agentaccount
    //         // $agentaccount = AgentAccount::where('agent_id', $agent->id)->first();

    //         // 取出openid，然后和合伙人表进行绑定
    //         // 如果缓存不存在，那么就不写入
    //         // 数据库写入openid，进行绑定
    //         // 但是为了防止别人登录自己的账号进行查看，这个时候要对openid进行锁定，如果判断数据库中存在openid，那么就不需要重复写入了
    //         // 这两个一般同时为空，或者同时不为空，一起判断即可
    //         if (empty($agent->wx_openid) && empty($agent->openid)) {
    //             // 如果同时为空，那么就写入openid信息
    //             $agent->update(compact('wx_openid', 'openid'));
    //         }

    //         // 写入session
    //         $request->session()->put('agent', [
    //             // 因为用户信息经常变，所以加入缓存不合适，改为每次都从数据库查询比较稳妥
    //             // 'agent_model' => $agent,
    //             'agent_mobile' => $user['mobile'],
    //             'agent_id' => $agent['id'],
    //             'agent_name' => $agent['name'],
    //             'agent_openid' => $wx_openid,
    //             'agent_wxuser' => $wx_user,
    //         ]);

    //         // 因为已经登录成功，所以无需记录错误登录次数了，删除即可。
    //         if (Redis::exists($ip)) {
    //             Redis::del($ip);
    //         }

    //         // 渲染
    //         $response = [
    //             'code' => '0',
    //             'msg' => '登录成功',
    //             'wx_user' => $wx_user,
    //             // 单笔提现手续费
    //             'per_charge' => $method->per_charge,
    //         ];
    //         return $response;
    //     } else {
    //         // 如果登录失败，那么就记录其失败次数，超过3次，就出现验证码
    //         // 如果存在，则加1，如果不存在，则初始化为1
    //         if (Redis::exists($ip)) {
    //             Redis::incr($ip);
    //         } else {
    //             Redis::set($ip, 1);
    //         }
    //         $response = [
    //             'code' => '1',
    //             'msg' => '用户名或密码不正确',
    //             'login_ip' => $ip,
    //             'login_count' => Redis::get($ip),
    //         ];
    //         return $response;
    //     }
    // }


    // /**
    //  * 微信-登录默认首页
    //  */
    // public function wxindex(Request $request)
    // {
    //     // 因为用用户主动授权，所以下面的注释掉
    //     // 取出openid
    //     // $openid = $this->getOpenId();
    //     // // 渲染
    //     // return view('agent.wx', compact('openid'));

    //     // // 取出授权user
    //     // $user = $this->getauthuser();
    //     // // 渲染
    //     // return view('agent.wx', compact('user'));

    //     // 逻辑
    //     // 拿到配置参数，传入分享相关
    //     $app_id = $this->config['app_id'];
    //     $secret = $this->config['secret'];
    //     $user = $this->getauthuser();
    //     // 渲染
    //     return view('agent.wx', compact('app_id', 'secret', 'user'));
    // }


    /**
     * 微信公众号默认首页
     */
    public function wxindex()
    {
        // 逻辑
        // 拿到配置参数
        $app_id = $this->config['app_id'];
        $secret = $this->config['secret'];
        $user = $this->getauthuser();
        // 微信jssdk
        $signPackage = $this->getSignPackage($user['id']);
        // 首页授权添加
        $method = '5';
        // 默认密码123456
        $password = bcrypt('123456');
        // 微信相关
        $wx_openid = $user['id'];
        $openid = $user['id'];

        // 判断是否有parentopenid或者invite_openid
        if (!empty($this->request->parentopenId)) {
            $parentopenid = $this->request->parentopenId;
        } else {
            // 再判断invite_openid是否存在
            if (!empty($this->request->invite_openid)) {
                $parentopenid = $this->request->invite_openid;
            } else {
                $parentopenid = null;
            }
        }

        // 再判断如果$openid和$parentopenid相等，说明自己邀请自己，那么邀请无效，$parentopenid为NULL
        if ($parentopenid == $openid) {
            $parentopenid = null;
        }

        // 判断上级合伙人是否存在，如果不存在则邀请无效，上级合伙人留空
        if (!empty($parentopenid)) {
            $parent_agent = $this->getAgent($parentopenid);
            // 如果合伙人不存在，则parentopenid留空
            if (!$parent_agent) {
                $parentopenid = null;
            }
        }

        // 保存
        // 因为要记录两次，所以这里启用事务处理
        DB::beginTransaction();
        try {
            // 把信息写入合伙人表
            $agent = $this->getAgent($user['id']);
            // 如果不存在，就写入
            if (!$agent) {
                $wx_openid = $user['id'];
                $openid = $user['id'];
                // 产生新合伙人
                $agent = Agent::create(compact('wx_openid', 'openid', 'parentopenid', 'method', 'password'));
                if (!$agent) {
                    throw new \Exception('创建合伙人失败');
                }

                // 写入sid值
                $agent_id = $agent->id;
                $result = \DB::table('agents')->select(\DB::raw("concat('M', right(concat('00000' , id), 5)) as sid"))->where('id', $agent_id)->get();
                $sid = $result[0]->sid;

                // 如果没有sid值，那么就报错
                if (!$sid) {
                    throw new \Exception('生成合伙人编号失败');
                }
                if (!$agent->update(compact('sid'))) {
                    throw new \Exception('更新合伙人编号失败');
                }

                // 如果agentaccount表没有这个用户，那么就新增
                if (!AgentAccount::firstOrCreate(['agent_id' => $agent_id], [
                    'frozen_money'    => '0.00',
                    'available_money' => '0.00',
                    'sum_money'       => '0.00',
                ])) {
                    throw new \Exception('写入合伙人资产表失败');
                }

                // 判断是否存在有效的parentopenid，如果有，那么就更新缓存，待更新...
                if ($parentopenid) {
                    // parent_agent缓存过期处理
                    $this->deleteSonAgentsCache($parentopenid);
                    // 重新生成
                    $this->createSonAgentsCache($parentopenid);
                }

                // 生成当前合伙人缓存
                $this->createAgentCache($openid);
            }

            // 把agent信息记录Session
            $agent_arr = $agent->toArray();
            if (!Session::get('agent')) {
                Session::put('agent', $agent_arr);
            }

            // 提交
            DB::commit();

            // 记录日志
            $msg = '';
            $msg .= '<pre>' . PHP_EOL;
            $msg .= '微信新用户授权注册为合伙人操作成功，该用户的主要信息如下：' . PHP_EOL;
            $arr = print_r($user, true);
            $msg .= "$arr";
            $msg .= PHP_EOL . PHP_EOL;
            $msg .= '该合伙人的信息如下：' . PHP_EOL;
            $arr = print_r($agent_arr, true);
            $msg .= "$arr";
            $msg .= PHP_EOL . PHP_EOL;
            // 写入日志
            Log::info($msg);

        } catch (\Exception $e) {
            // 回滚
            DB::rollback();
            // 错误返回
            // 记录日志
            $msg = '';
            $msg .= '<pre>' . PHP_EOL;
            $msg .= '微信新用户授权注册为合伙人操作失败，该用户的主要信息如下：' . PHP_EOL;
            $arr = print_r($user, true);
            $msg .= "$arr";
            $msg .= PHP_EOL . PHP_EOL;
            $msg .= '错误信息：' . PHP_EOL;
            $msg .= $e->getMessage();
            $msg .= PHP_EOL . PHP_EOL;
            // 写入日志
            Log::info($msg);
        }

        // 渲染
        return view('agent.wx', compact('user', 'app_id', 'secret', 'signPackage'));
    }


    /**
     * 微信-邀请页面
     */
    public function wxinvitation()
    {
        // 拿到配置参数
        $app_id = $this->config['app_id'];
        $secret = $this->config['secret'];
        $user = $this->getauthuser();
        // 微信jssdk
        $signPackage = $this->getSignPackage($user['id']);
        // 渲染
        return view('agent.invitation', compact('app_id', 'secret', 'user', 'signPackage'));
    }

    /**
     * 微信-激励金明细
     */
    public function wxincentivedetail()
    {
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('agent.incentivedetail', compact('user'));
    }


    /**
     * 微信-我的默认首页
     */
    public function wxmine()
    {
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('agent.wxmine', compact('user'));
    }

    /**
     * 微信-提现明细列表
     */
    public function wxwithdraw()
    {
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('agent.withdraw', compact('user'));
    }

    /**
     * 微信-申请提现
     */
    public function wxdrawcash()
    {
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('agent.drawcash', compact('user'));
    }

    /**
     * 微信-进度查询
     */
    public function wxprogress()
    {
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('agent.progress', compact('user'));
    }

    /**
     * 微信-我的订单
     */
    public function wxorder()
    {
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('agent.order', compact('user'));
    }

    /**
     * 微信-我，接口，根据openid查询
     */
    public function wxmybyopenid($openid)
    {
        // 逻辑
        // $agent = Agent::select(['id', 'name', 'mobile', 'cash_password', 'wx_openid'])->where('openid', $openid)->first();
        $agent = $this->getAgent($openid);
        // 如果没有登录，那么在数据库中肯定找不到openid的记录，需要判断
        if ($agent) {
            $account = AgentAccount::select(['available_money', 'frozen_money', 'cash_money', 'sum_money'])->where('agent_id', $agent->id)->first();
        } else {
            $account = null;
        }
        // 返回结果
        $data = [
            'agent'   => $agent,
            'account' => $account,
        ];
        return $data;
    }

    /**
     * 微信-我，接口，根据openid查询
     */
    public function wxmy()
    {
        // 逻辑
        $openid = $this->request->openid;
        $agent = $this->getAgent($openid);
        if ($agent) {
            // 取出agent_id
            $agent_id = $agent->id;
            $account = AgentAccount::firstOrCreate(['agent_id' => $agent_id], [
                'frozen_money'    => '0.00',
                'available_money' => '0.00',
                'sum_money'       => '0.00',
            ]);
            // 代付通道
            $method = $this->getAdvanceMethod();
            // 返回
            $response = [
                'agent'   => $agent,
                'account' => $account,
                'method'  => $method,
                'code'    => '0',
                'msg'     => '合伙人存在',
            ];
        } else {
            $response = [
                'agent'   => null,
                'account' => null,
                'method'  => null,
                'code'    => '1',
                'msg'     => '合伙人不存在',
            ];
        }
        // 最终返回
        return $response;
    }


    /**
     * 微信-设置提现密码
     */
    public function setpwd()
    {
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('agent.setpwd', compact('user'));
    }

    /**
     * 微信-修改提现密码
     */
    public function modifypwd()
    {
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('agent.modifypwd', compact('user'));
    }

    /**
     * 微信-我的信息
     */
    public function wxmessage()
    {
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('agent.message', compact('user'));
    }

    /**
     * 微信-我的客服
     */
    public function wxcustomerService()
    {
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('agent.customservice', compact('user'));
    }

    /**
     * 微信-设置提现密码
     */
    public function wxsetpwd(Request $request, $id)
    {
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('agent.setpwd', compact('user', 'id'));
    }

    /**
     * 微信-设置提现密码逻辑 [必须登录]
     */
    public function wxstorepwd(Request $request, $id)
    {
        // 验证
        $this->validate($request, [
            'cash_password' => 'required|digits:6|confirmed',
        ]);

        // 逻辑
        $cash_password = bcrypt(request('cash_password'));
        $agent = Agent::find($id);
        if ($agent->update(compact('cash_password'))) {
            // 更新当前合伙人缓存
            $this->deleteAgentCache($agent->openid);
            $this->createAgentCache($agent->openid);
            // 返回数据
            $data = [
                'code' => '0',
                'msg'  => '提现密码设置成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg'  => '提现密码设置失败',
            ];
        }
        return $data;
    }

    /**
     * 微信-修改提现密码
     */
    public function wxmodifypwd(Request $request, $id)
    {
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('agent.modifypwd', compact('user', 'id'));
    }

    /**
     * 微信-修改提现密码逻辑 [必须登录]
     */
    public function wxupdatepwd(Request $request, $id)
    {
        // 验证
        $this->validate($request, [
            'mobile'        => 'regex:/^1[34578][0-9]{9}$/',
            'password'      => 'required',
            'cash_password' => 'required|digits:6|confirmed',
        ]);

        // 逻辑
        // $user = request(['mobile', 'password']);
        // 手机号
        $mobile = request('mobile');
        // 旧提现密码
        $password = request('password');
        // 新提现密码
        $cash_password = bcrypt(request('cash_password'));

        // 取出当前用户模型
        $agent = Agent::find($id);

        // 判断是否和数据库匹配
        // 如果不匹配
        if (!Hash::check($password, $agent->cash_password)) {
            $data = [
                'code' => '1',
                'msg'  => '手机号或提现密码不正确，请重新输入！',
            ];
        } else {
            // 如果匹配，就修改
            if ($agent->update(compact('cash_password'))) {

                // 更新当前合伙人缓存
                $this->deleteAgentCache($agent->openid);
                $this->createAgentCache($agent->openid);

                // 返回数据
                $data = [
                    'code' => '0',
                    'msg'  => '提现密码修改成功',
                ];
            } else {
                $data = [
                    'code' => '1',
                    'msg'  => '提现密码修改失败',
                ];
            }
        }
        // 返回结果
        return $data;
    }


    /**
     * 微信-忘记提现密码
     */
    public function wxresetpwd(Request $request, $id)
    {
        // 逻辑
        $agent = Agent::find($id);
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('agent.resetpwd', compact('id', 'agent', 'user'));
    }

    /**
     * 微信-处理忘记密码逻辑
     */
    public function wxupdateresetpwd(Request $request, $id)
    {
        // 验证
        $this->validate($request, [
            'id_number'     => 'required',
            'cash_password' => 'required|digits:6|confirmed',
        ]);

        // 逻辑
        $id_number = request('id_number');
        // 新提现密码
        $cash_password = bcrypt(request('cash_password'));

        // 取出当前用户模型
        $agent = Agent::find($id);

        // 查找身份证和手机号是否匹配
        // 如果不匹配
        if ($agent->id_number != $id_number) {
            $data = [
                'code' => '-1',
                'msg'  => '身份证号码和本人不匹配，请重新输入！',
            ];
        } else {
            // 如果匹配，就修改
            if ($agent->update(compact('cash_password'))) {

                // 更新当前合伙人缓存
                $this->deleteAgentCache($agent->openid);
                $this->createAgentCache($agent->openid);

                // 返回
                $data = [
                    'code' => '0',
                    'msg'  => '提现密码修改成功',
                ];
            } else {
                $data = [
                    'code' => '1',
                    'msg'  => '提现密码修改失败',
                ];
            }
        }
        // 返回结果
        return $data;
    }



    /**
     * 微信-忘记登录密码
     */
    // public function wxresetloginpwd()
    // {
    //     // 取出授权user
    //     $user = $this->getauthuser();
    //     // 渲染
    //     return view('agent.resetloginpwd', compact('user'));
    // }

    /**
     * 微信-重置登录密码逻辑
     */
    // public function wxupdateresetloginpwd(Request $request)
    // {
    //     // 验证
    //     $this->validate($request, [
    //         'mobile' => 'regex:/^1[34578][0-9]{9}$/',
    //         'openid' => 'required|string',
    //         'password' => 'required|confirmed',
    //     ]);

    //     // 逻辑
    //     $openid = $request->openid;
    //     $mobile = $request->mobile;
    //     $password = bcrypt(request('password'));
    //     $agent = Agent::where('openid', $openid)->first();
    //     if (!$agent) {
    //         $data = [
    //             'code' => '-1',
    //             'msg' => '您还没有注册为合伙人，无需找回密码！',
    //         ];
    //         return $data;
    //     }

    //     // 判断手机号是否正确
    //     if ($agent->mobile != $mobile) {
    //         $data = [
    //             'code' => '-1',
    //             'msg' => '手机号码不匹配，请重新输入！',
    //         ];
    //     } else {
    //         if ($agent->update(compact('password'))) {
    //             $data = [
    //                 'code' => '0',
    //                 'msg' => '登录密码修改成功',
    //             ];
    //         } else {
    //             $data = [
    //                 'code' => '1',
    //                 'msg' => '登录密码修改失败',
    //             ];
    //         }
    //     }
    //     // 返回数组
    //     return $data;
    // }


    /**
     * 微信-修改登录密码
     */
    // public function wxmodifyloginpwd(Request $request, $id)
    // {
    //     // 取出授权user
    //     $user = $this->getauthuser();
    //     // 渲染
    //     return view('agent.modifyloginpwd', compact('user', 'id'));
    // }

    /**
     * 微信-修改登录密码逻辑 [必须登录]
     */
    // public function wxupdateloginpwd(Request $request, $id)
    // {
    //     // 验证
    //     $this->validate($request, [
    //         'mobile' => 'regex:/^1[34578][0-9]{9}$/',
    //         'password' => 'required',
    //         'login_password' => 'required|confirmed',
    //     ]);

    //     // 逻辑
    //     $user = request(['mobile', 'password']);
    //     $password = bcrypt(request('login_password'));
    //     // 判断是否和数据库匹配
    //     if (\Auth::guard('agent')->attempt($user)) {
    //         if (Agent::find($id)->update(compact('password'))) {
    //             $data = [
    //                 'code' => '0',
    //                 'msg' => '登录密码修改成功',
    //             ];
    //         } else {
    //             $data = [
    //                 'code' => '1',
    //                 'msg' => '登录密码修改失败',
    //             ];
    //         }
    //     } else {
    //         $data = [
    //             'code' => '1',
    //             'msg' => '手机号或登录密码不正确',
    //         ];
    //     }
    //     // 返回数组
    //     return $data;
    // }


    /**
     * 微信-账户详情
     */
    public function wxmysum()
    {
        // 获取授权用户信息
        $user = $this->getauthuser();
        // 渲染
        return view('agent.wxmysum', compact('user'));
    }

    /**
     * 微信-设置
     */
    public function wxsetting()
    {
        // 获取授权用户信息
        $user = $this->getauthuser();
        // 渲染
        return view('agent.setting', compact('user'));
    }

    /**
     * 微信-退出登录，已经作废
     */
    // public function wxlogout(Request $request)
    // {
    //     \Auth::guard('agent')->logout();
    //     // 如果redis中存在此session，那么就删除
    //     if ($request->session()->has('agent')) {
    //         $request->session()->forget('agent');
    //     }
    //     $data = [
    //         'code' => '0',
    //         'msg' => '您已成功退出登录',
    //     ];
    //     return $data;
    // }

    /**
     * 微信-卡号列表
     */
    public function wxcards()
    {
        // 逻辑
        // 判断合伙人
        $agentResult = $this->wxcheckbyopenid();
        if ($agentResult['code'] != '0') {
            $response = [
                'code'   => $agentResult['code'],
                'msg'    => $agentResult['msg'],
                'data'   => null,
                'length' => '0',
            ];
            return $response;
        }
        // 取出合伙人
        $agent = $agentResult['data'];
        // 取出合伙人id
        $agent_id = $agent->id;

        // 取出默认卡
        $cards = Card::select(['id', 'card_number', 'bank_id', 'isdefault'])->where('agent_id', $agent_id)->where('isdefault', '1')->get();
        foreach ($cards as $k => $card) {
            $bankinfo = BankCard::info($card->card_number);
            $cards[$k]->bankName = $bankinfo['bankName'];
            $cards[$k]->bankImg = $bankinfo['bankImg'];
            $cards[$k]->new_card_number = $this->substr_cut($card->card_number);
        }

        // 返回
        $response = [
            'data'   => $cards,
            'length' => $cards->count(),
        ];
        return $response;

    }

    /**
     * 微信-设置当前卡号（唯一），但是必须是默认卡
     */
    public function wxfirstcard()
    {
        // 逻辑
        // 判断合伙人
        $agentResult = $this->wxcheckbyopenid();
        if ($agentResult['code'] != '0') {
            $response = [
                'code'   => $agentResult['code'],
                'msg'    => $agentResult['msg'],
                'data'   => null,
                'length' => '0',
            ];
            return $response;
        }
        // 取出合伙人
        $agent = $agentResult['data'];
        // 取出合伙人id
        $agent_id = $agent->id;

        // 查找模型
        $card = Card::select(['id', 'card_number', 'bank_id', 'isdefault'])->where('agent_id', $agent_id)->where('isdefault', '1')->first();
        if ($card === null) {
            $response = [
                'data'   => $card,
                'length' => '0',
            ];
        } else {
            // 取出卡片相关资料
            // 因为已经把卡片的相关信息放进数据库了，所以不需要用接口再查询一次了
            $card->bankName = $card->bank->name;
            $card->new_card_number = $this->substr_cut($card->card_number);
            // $bankinfo = BankCard::info($card->card_number);
            // $card->bankName = $bankinfo['bankName'];
            // $card->bankImg = $bankinfo['bankImg'];
            // $card->new_card_number = $this->substr_cut($card->card_number);
            $response = [
                'data'   => $card,
                'length' => '1',
            ];
        }
        // 返回
        return $response;
    }

    /**
     * 微信-卡号列表(模板用)
     */
    public function wxrankcard()
    {
        // 获取授权用户信息
        $user = $this->getauthuser();
        // 渲染
        return view('agent.rankcard', compact('user'));
    }

    /**
     * 微信-卡号列表(模板用)
     */
    public function checkuser(Request $request)
    {
        // 验证
        $this->validate($request, [
            'realName' => 'required|string',
        ]);
        // 逻辑
        $name = request('realName');
        // 判断合伙人
        $agentResult = $this->wxcheckbyopenid();
        if ($agentResult['code'] != '0') {
            $response = [
                'code' => $agentResult['code'],
                'msg'  => $agentResult['msg'],
            ];
            return $response;
        }
        // 取出合伙人
        $agent = $agentResult['data'];

        // 判断姓名是否匹配
        // 如果不匹配，就提示禁止绑卡
        if ($name !== $agent->name) {
            $response = [
                'code' => '1',
                'msg'  => '抱歉，您只能绑定自己的银行卡！',
            ];
        } else {
            // 否则就允许绑卡
            $response = [
                'code' => '0',
                'msg'  => '姓名一致性验证通过',
            ];
        }
        return $response;
    }

    /**
     * PC-给合伙人添加卡号
     */
    public function addcard()
    {
        // 渲染
        return view('agent.cardadd');
    }

    /**
     * PC-给合伙人添加卡号逻辑
     */
    public function addcardstore()
    {
        // 验证
        $this->validate($request, [
            'card_number' => 'required|unique:cards,card_number',
        ]);

        // 逻辑
        $card_number = request('card_number');
        $agent_id = \Session::get('agent')['agent_id'];
        // 卡号信息
        $cardinfo = BankCard::info($card_number);
        // 尾号
        $strpos_card = substr($card_number, -4);
        // 验证卡号
        if ($cardinfo['validated'] == false) {
            $data = [
                'code' => '1',
                'msg'  => '银行卡号错误，请重新输入',
            ];
            return $data;
        }
        // 开户行
        // 判断卡信息开户行是否存在
        $bank_id = Bank::firstOrCreate(['name' => $cardinfo['bankName']])->id;
        $branch = '未填写';
        $isdefault = 0;
        $newid = Card::create(compact('agent_id', 'bank_id', 'branch', 'isdefault', 'card_number'))->id;
        if ($newid) {
            $response = [
                'code' => '0',
                'msg'  => '银行卡添加成功',
            ];
            return $response;
        } else {
            $response = [
                'code' => '1',
                'msg'  => '银行卡添加失败',
            ];
            return $response;
        }
    }

    /**
     * 微信-给合伙人添加卡号
     */
    public function wxaddcard()
    {
        // 获取授权用户信息
        $user = $this->getauthuser();
        // 渲染
        return view('agent.cardadd', compact('user'));
    }

    /**
     * 微信-给合伙人添加卡号逻辑
     */
    public function wxaddcardstore(Request $request)
    {
        // 判断合伙人
        $agentResult = $this->wxcheckbyopenid();
        if ($agentResult['code'] != '0') {
            $response = [
                'code' => $agentResult['code'],
                'msg'  => $agentResult['msg'],
            ];
            return $response;
        }
        // 取出合伙人
        $agent = $agentResult['data'];
        // 取出合伙人id
        $agent_id = $agent->id;

        // 验证
        $this->validate($request, [
            'card_number' => 'required|unique:cards,card_number,' . $agent_id,
            // 'name' => 'required|string',
            // 'id_number' => 'required|unique:agents,id_number,'.$agent_id,
        ]);

        // 逻辑
        $card_number = request('card_number');
        // $name = request('name');
        // $id_number = request('id_number');
        // 卡号信息
        $cardinfo = BankCard::info($card_number);
        // 尾号
        $strpos_card = substr($card_number, -4);
        // 验证卡号
        if ($cardinfo['validated'] == false) {
            $data = [
                'code' => '1',
                'msg'  => '银行卡号错误，请重新输入',
            ];
            return $data;
        }

        // 开启事务机制
        DB::beginTransaction();
        try {
            // 判断卡信息开户行是否存在
            $bank_id = Bank::firstOrCreate(['name' => $cardinfo['bankName']])->id;
            if (!$bank_id) {
                throw new \Exception('开户行不存在');
            }

            $branch = '未填写';
            // 用户自己添加的也为默认卡号
            $isdefault = '1';
            // $newid = Card::create(compact('agent_id', 'bank_id', 'branch', 'isdefault', 'card_number'))->id;
            $card = Card::create(compact('agent_id', 'bank_id', 'branch', 'isdefault', 'card_number'));
            if (!$card) {
                throw new \Exception('银行卡添加失败');
            }

            // agent更新，不用更新了，实名认证已经完毕了
            // if (!Agent::find($agent_id)->update(compact('name', 'id_number'))) {
            //     throw new \Exception('更新用户信息失败');
            // }

            // 如果都无错，则提交
            DB::commit();

            // 返回
            $response = [
                'code' => '0',
                'msg'  => '银行卡添加成功',
            ];
            return $response;

        } catch (\Exception $e) {
            // 回滚
            DB::rollback();
            $data = [
                'code' => '1',
                'msg'  => $e->getMessage(),
            ];
            return $data;
        }
    }


    /**
     * 微信-编辑卡号
     */
    public function wxeditcard(Request $request, $id)
    {
        // 获取授权用户信息
        $user = $this->getauthuser();
        // 渲染
        $card = Card::find($id);
        return view('agent.cardedit', compact('card', 'user'));
    }


    /**
     * PC-更新卡号逻辑
     */
    public function updatecard(Request $request, $id)
    {
        // 验证
        $this->validate($request, [
            'card_number' => 'required|unique:cards,card_number,' . $id,
            'isdefault'   => 'required|integer',
        ]);

        // 逻辑
        $card_number = request('card_number');
        $isdefault = empty(request('isdefault')) ? 0 : 1;
        $agent_id = Session::get('agent')['agent_id'];
        $card = Card::find($id);

        // 卡号信息
        $cardinfo = BankCard::info($card_number);
        // 验证卡号
        if ($cardinfo['validated'] == false) {
            $data = [
                'code' => '1',
                'msg'  => '银行卡号错误，请重新输入',
            ];
            return $data;
        }
        // 开户行
        // 判断卡信息开户行是否存在
        DB::beginTransaction();
        try {
            // 如果待修改的银行卡不是默认卡，那么就不要清除原来的默认属性，否则就清除
            if ($isdefault) {
                // 首先把默认的全部清零
                Card::where('agent_id', $agent_id)->update([
                    'isdefault' => '0',
                ]);
            }

            $bank_id = Bank::firstOrCreate(['name' => $cardinfo['bankName']])->id;
            if (!$bank_id) {
                throw new \Exception('添加开户行失败');
            }

            $result = $card->update(compact('isdefault', 'card_number', 'bank_id'));

            if (!$result) {
                throw new \Exception('银行卡修改失败');
            }

            // 提交
            DB::commit();

            // 返回
            $data = [
                'code' => '0',
                'msg'  => '银行卡修改成功',
            ];
            return $data;

        } catch (\Exceptoin $e) {
            DB::rollback();
            $data = [
                'code' => '1',
                'msg'  => $e->getMessage(),
            ];
            return $data;
        }
    }


    /**
     * 微信-更新卡号逻辑
     */
    public function wxupdatecard(Request $request, $id)
    {
        // 验证
        $this->validate($request, [
            'card_number' => 'required|unique:cards,card_number,' . $id,
        ]);

        // 逻辑
        $card_number = request('card_number');
        // 因为微信只能绑定一张卡，所以isdefault永远为1，也就是默认的
        // 下面的也可以不写
        $isdefault = 1;
        // $agent_id = Session::get('agent')['agent_id'];
        $card = Card::find($id);

        // 卡号信息
        $cardinfo = BankCard::info($card_number);
        // 验证卡号
        if ($cardinfo['validated'] == false) {
            $data = [
                'code' => '1',
                'msg'  => '银行卡号错误，请重新输入',
            ];
            return $data;
        }
        // 开户行
        // 判断卡信息开户行是否存在
        DB::beginTransaction();
        try {
            // // 如果待修改的银行卡不是默认卡，那么就不要清除原来的默认属性，否则就清除
            // if ($isdefault) {
            //     // 首先把默认的全部清零
            //     Card::where('agent_id', $agent_id)->update([
            //         'isdefault' => '0',
            //     ]);
            // }

            $bank_id = Bank::firstOrCreate(['name' => $cardinfo['bankName']])->id;
            if (!$bank_id) {
                throw new \Exception('添加开户行失败');
            }

            $result = $card->update(compact('isdefault', 'card_number', 'bank_id'));

            if (!$result) {
                throw new \Exception('银行卡修改失败');
            }

            // 提交
            DB::commit();

            // 返回
            $data = [
                'code' => '0',
                'msg'  => '银行卡修改成功',
            ];
            return $data;

        } catch (\Exceptoin $e) {
            DB::rollback();
            $data = [
                'code' => '1',
                'msg'  => $e->getMessage(),
            ];
            return $data;
        }
    }

    /**
     * 微信-删除卡号逻辑 【作废】
     */
    public function wxcarddelete($cardid)
    {
        // 模型
        $card = Card::find($cardid);
        // 如果模型不存在，那么就删除失败
        if (!$card->count()) {
            $data = [
                'code' => '1',
                'msg'  => '该记录不存在，删除失败',
            ];
            return $data;
        }

        if ($card->delete()) {
            $data = [
                'code' => '0',
                'msg'  => '删除成功',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg'  => '删除失败',
            ];
        }
        return $data;
    }

    /**
     * PC-所有的银行卡列表
     */
    public function cards()
    {
        // 逻辑
        $cards = Card::select(['id', 'card_number', 'bank_id', 'isdefault'])->where('agent_id', Session::get('agent')['agent_id'])->orderBy('id', 'asc')->get();
        foreach ($cards as $k => $card) {
            $cards[$k]->bankName = $card->bank->name;
            $cards[$k]->lastNumber = substr($card->card_number, -4);
        }
        return $cards;

        // 逻辑
        // $openid = $this->request->openid;
        // $agent = Agent::where('openid', $openid)->first();
        // $cards = Card::select(['id', 'card_number', 'bank_id', 'isdefault'])->where('agent_id', $agent->id)->orderBy('id', 'asc')->where('isdefault', '1')->get();
        // foreach ($cards as $k => $card) {
        //     $cards[$k]->bankName = $card->bank->name;
        //     $cards[$k]->lastNumber = substr($card->card_number, -4);
        // }
        // return $cards;
    }


    /**
     * 根据银行卡ID请求其模型，单页使用
     */
    public function cardinfo(Request $request, $id)
    {
        $card = Card::select(['id', 'card_number', 'isdefault'])->find($id);
        return $card;
    }


    /**
     * 提现记录列表
     */
    public function withdraws()
    {
        // 判断合伙人
        $agentResult = $this->wxcheckbyopenid();
        if ($agentResult['code'] != '0') {
            $response = [
                'code'   => $agentResult['code'],
                'msg'    => $agentResult['msg'],
                'data'   => null,
                'length' => '0',
            ];
            return $response;
        }
        // 取出合伙人
        $agent = $agentResult['data'];
        // 取出合伙人id
        $agent_id = $agent->id;
        // 逻辑
        $withdraws = Withdraw::select(['id', 'account', 'sum', 'updated_at', 'status', 'err_msg', 'err_code'])->where('agent_id', $agent_id)->orderBy('updated_at', 'desc')->get();
        // 数据加工
        if ($withdraws->count()) {
            foreach ($withdraws as $k => $withdraw) {
                switch ($withdraw->status) {
                    case '0':
                        $withdraws[$k]->status_name = '结算中';
                        break;
                    case '1':
                        $withdraws[$k]->status_name = '结算成功';
                        break;
                    case '2':
                        $withdraws[$k]->status_name = '结算失败';
                        break;
                    default:
                        $withdraws[$k]->status_name = '结算中';
                }
            }
            $response = [
                'data'   => $withdraws,
                'length' => $withdraws->count(),
            ];
        } else {
            $response = [
                'data'   => null,
                'length' => 0,
            ];
        }

        // 返回
        return $response;
    }


    /**
     * 判断合伙人是否已经登录(PC端)
     */
    public function islogin()
    {
        return Session::get('agent')['agent_id'];
    }

    /**
     * 判断合伙人是否已经登录(微信端)，因为已经修改为前端验证，所以这个逻辑作废了
     */
    // public function iswxlogin()
    // {
    //     if (Session::get('agent')['agent_openid']) {
    //         $response = [
    //             'code' => '0',
    //             'data' => Session::get('agent')['agent_openid'],
    //             'msg' => '已登录',
    //         ];
    //     } else {
    //         $response = [
    //             'code' => '1',
    //             'msg' => '未登录',
    //         ];
    //     }
    //     return $response;
    // }


    /**
     * 获取openid
     */
    public function getOpenId()
    {
        //如果已经获取到用户的openId就存储在session中
        if (Session::has('openid')) {
            return Session::get('openid');
        } else {
            //1.用户访问微信服务器地址 先获取到微信get方式传递过来的code
            //2.根据code获取到openID
            if (!isset($_GET['code'])) {
                //没有获取到微信返回来的code ，让用户再次访问微信服务器地址

                //redirect_uri 解释
                //跳转地址：你发起请求微信服务器获取code ，
                //微信服务器返回来给你的code的接收地址（通常就是发起支付的页面地址）

                //组装跳转地址，如果不能跳转，那么就换回来
                $redirect_uri = self::CODEURL . 'appid=' . $this->config['app_id'] . '&redirect_uri=' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&response_type=code&scope=' . self::SCOPE . '&state=STATE#wechat_redirect';

                //跳转获取code
                header("location:{$redirect_uri}");

            } else {
                //调用接口获取openId
                $openidurl = self::OPENIDURL . 'appid=' . $this->config['app_id'] . '&secret=' . $this->config['secret'] . '&code=' . $_GET['code'] . '&grant_type=authorization_code&r=' . rand(1, 999999999);

                //请求获取用户的openID
                $data = file_get_contents($openidurl);
                $arr = json_decode($data, true);

                //获取到的openid保存到session中
                $this->request->session()->put('openid', $arr['openid']);

                // 再取出
                return Session::get('openid');
            }
        }

    }

    // 获取令牌
    public function getAccessToken()
    {
        $curl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->config['app_id'] . '&secret=' . $this->config['secret'];
        $content = $this->_request($curl);
        $cont = json_decode($content);
        return $cont->access_token;
    }

    /**
     * 通过openid拉取用户信息
     * @param string $openid [description]
     * @return [type]         [description]
     */
    public function getUserInfo()
    {
        // 判断是否redis中存在
        if (Session::has('wechat.oauth_user')) {
            return Session::get('wechat.oauth_user');
        } else {
            $openid = $this->getOpenId();
            $access_token = $this->getAccessToken();
            $urlStr = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN';
            $url = sprintf($urlStr, $access_token, $openid);
            $result = json_decode($this->_request($url), true);
            //获取到的openid保存到session中
            $this->request->session()->put('wechat.oauth_user', $result);
            // 再取出
            return Session::get('wechat.oauth_user');
        }
    }

    /**
     * 设置网络请求
     */
    public function _request($curl, $https = true, $method = 'GET', $data = null)
    {
        // 创建一个新cURL资源
        $ch = curl_init();

        // 设置URL和相应的选项
        curl_setopt($ch, CURLOPT_URL, $curl);    //要访问的网站
        curl_setopt($ch, CURLOPT_HEADER, false);    //启用时会将头文件的信息作为数据流输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  //将curl_exec()获取的信息以字符串返回，而不是直接输出。

        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  //FALSE 禁止 cURL 验证对等证书（peer's certificate）。
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);  //验证主机，设为0表示不检查证书，设为1表示检查证书中是否有CN(common name)字段，设为2表示在1的基础上校验当前的域名是否与CN匹配
        }
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);  //发送 POST 请求
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  //全部数据使用HTTP协议中的 "POST" 操作来发送。
        }

        // 抓取URL并把它传递给浏览器
        $content = curl_exec($ch);
        if ($content === false) {
            return "网络请求出错: " . curl_error($ch);
            exit();
        }
        //关闭cURL资源，并且释放系统资源
        curl_close($ch);

        // 返回
        return $content;
    }

    //将卡号进行处理，中间用星号表示
    public function substr_cut($str)
    {
        //获取字符串长度
        $strlen = mb_strlen($str, 'utf-8');
        //如果字符创长度小于2，不做任何处理
        if ($strlen < 2) {
            return $str;
        } else {
            //mb_substr — 获取字符串的部分
            $firstStr = mb_substr($str, 0, 4, 'utf-8');
            $lastStr = mb_substr($str, -4, 4, 'utf-8');
            //str_repeat — 重复一个字符串
            return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($str, 'utf-8') - 4) : $firstStr . str_repeat("*", $strlen - 8) . $lastStr;
        }
    }

    /**
     * 银行四要素认证接口
     */
    public function checkbankcard()
    {
        // 接收数据
        $name = $this->request->name;
        $idcardno = $this->request->idcardno;
        $bankcardno = $this->request->bankcardno;
        $tel = $this->request->tel;
        $openid = $this->request->openid;
        // 请求url
        $url = 'http://api.id98.cn/api/v2/bankcard?appkey=' . self::MSG_APPKEY . '&name=' . $name . '&idcardno=' . $idcardno . '&bankcardno=' . $bankcardno . '&tel=' . $tel;

        // 判断合伙人
        $agentResult = $this->wxcheckbyopenid();
        if ($agentResult['code'] != '0') {
            $response = [
                'isok' => '1',
                'code' => '-1',
                'desc' => '抱歉，您没有权限请求四要素接口！',
                'data' => [
                    'bankcardno' => $bankcardno,
                    'name'       => $name,
                    'idcardno'   => $idcardno,
                    'tel'        => $tel,
                ],
            ];
            return $response;
        }

        // // 这里强制返回成功，Changing
        // // 如果是阳鸣天下，则返回成功，其他则返回失败
        // if ($openid == 'ol0Z1uJ8dkjU__z66lukgiZsNZl0') {
        //     $response = [
        //         'isok' => '1',
        //         'code' => '1',
        //         'desc' => '这是测试结果，持卡人认证成功',
        //         'data' => [
        //             'bankcardno' => $bankcardno,
        //             'name' => $name,
        //             'idcardno' => $idcardno,
        //             'tel' => $tel,
        //         ],
        //     ];
        // } else {
        //     $response = [
        //         'isok' => '1',
        //         'code' => '-1',
        //         'desc' => '这是测试结果，四要素认证失败！',
        //         'data' => [
        //             'bankcardno' => $bankcardno,
        //             'name' => $name,
        //             'idcardno' => $idcardno,
        //             'tel' => $tel,
        //         ],
        //     ];
        // }
        // // 返回
        // return $response;


        // 记录绑卡的url地址
        $msg = PHP_EOL . '绑卡报文如下：' . PHP_EOL;
        $msg .= '当前绑卡的用户openid：' . $openid . PHP_EOL;
        $msg .= '用户模型如下：' . PHP_EOL . PHP_EOL;
        $msg .= '<pre>' . PHP_EOL;
        $arr = print_r($agentResult, true);
        $msg .= "$arr";
        $msg .= '请求url地址：' . $url . PHP_EOL;
        // 写入日志
        Log::info($msg);

        // 返回
        // 取出结果
        $result = $this->http_curl($url);
        // 为了可控，需要获取当前短信是否发送成功的结果，把这个结果保存在日志文件中
        $msg = '';
        $msg .= '<pre>' . PHP_EOL;
        $msg .= '绑卡返回结果如下：' . PHP_EOL;
        $msg .= '绑卡时间：' . date('Y-m-d H:i:s') . PHP_EOL;
        $msg .= '最终结果：' . PHP_EOL;
        $arr = print_r($result, true);
        $msg .= "$arr";
        $msg .= PHP_EOL . PHP_EOL;
        // 写入日志
        Log::info($msg);
        // 返回给前端
        return $result;
    }

    /**
     * 发送4位数字验证码
     */
    public function createcode()
    {
        // 生成的验证码保存在cache里，默认120分钟有效期，测试用3000分钟
        Cache::put('wxyzm', mt_rand(1000, 9999), 5);
        return Cache::get('wxyzm');
    }

    /**
     * 临时获取4位数字验证码
     */
    public function getwxcode()
    {
        // 逻辑
        return Cache::get('wxyzm');
    }

    /**
     * 判断注册输入的短信验证码是否正确
     */
    public function checkregcode(Request $request)
    {
        // 逻辑
        $this->validate($request, [
            'capcha' => 'required',
        ], [
            'capcha.required' => '验证码不能为空',
        ]);
        $capcha = $request->get('capcha');
        if (Cache::get('wxyzm') == $capcha) {
            $data = [
                'code' => '0',
                'msg'  => '验证码输入正确',
            ];
        } else {
            $data = [
                'code' => '1',
                'yzm'  => Cache::get('wxyzm'),
                'msg'  => '验证码输入错误',
            ];
        }
        return $data;
    }

    /**
     * 发送短信-接口(和FinanceController中的方法重叠，待整合)
     */
    public function sendMsg()
    {
        // 测试url
        // $url = 'http://api.id98.cn/api/sms?appkey=d10a8e06284cf889deaf93ffb5d9c60a&phone=13800000000&templateid=1000&param=623584';
        // 接收数据
        $tel = $this->request->tel;
        $sendid = $this->request->sendid;
        $sendmsg = $this->request->sendmsg;
        $url = 'http://api.id98.cn/api/sms?appkey=' . self::MSG_APPKEY . '&phone=' . $tel . '&templateid=' . $sendid . '&param=' . $sendmsg;
        // 取出结果
        $result = $this->http_curl($url);
        // 为了可控，需要获取当前短信是否发送成功的结果，把这个结果保存在日志文件中
        $msg = '';
        $msg .= '<pre>' . PHP_EOL;
        $msg .= '短信计划发送时间：' . date('Y-m-d H:i:s') . PHP_EOL;
        $msg .= '短信发送url地址：' . $url . PHP_EOL . PHP_EOL;
        $msg .= '短信发送结果：' . PHP_EOL;
        $arr = print_r($result, true);
        $msg .= "$arr";
        $msg .= PHP_EOL . PHP_EOL;
        // 写入日志
        Log::info($msg);
        // 记录后返回给前端
        return $result;
    }

    /**
     * 判断当前用户是否被注册-接口
     */
    public function wxisreg()
    {
        // 手机号
        $mobile = $this->request->mobile;
        $agent = Agent::where('mobile', $mobile)->first();
        if ($agent) {
            $response = [
                'code' => '0',
                'msg'  => '手机号已被注册',
            ];
        } else {
            $response = [
                'code' => '1',
                'msg'  => '手机号可以注册',
            ];
        }
        // 返回
        return $response;
    }


    /**
     * 生成验证码
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
        Session::put('wxcode', $phrase);
        // 生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-Type:image/png");
        $builder->output();
    }

    /**
     * 取出当前系统保存的验证码(微信端)
     */
    public function getcaptcha()
    {
        return Session::get('wxcode');
    }


    /**
     * 判断注册输入的系统验证码是否正确
     */
    public function checklogincode(Request $request)
    {
        // 逻辑
        $this->validate($request, [
            'capcha' => 'required',
        ]);
        $capcha = $request->get('capcha');
        if (Session::get('wxcode') == $capcha) {
            $data = [
                'code' => '0',
                'msg'  => '验证码输入正确',
            ];
        } else {
            $data = [
                'code' => '1',
                'msg'  => '验证码输入错误',
            ];
        }
        return $data;
    }

    /**
     * 我的团队-v2接口，采用post传递
     * 根据openid查询，不需要登录
     */
    public function wxmyteamapi(Request $request)
    {
        // 验证
        $this->validate($request, [
            'openid' => 'required',
        ]);
        // 逻辑
        $openid = $this->request->openid;
        // 首先取出下级所有的合伙人
        $ids = $this->getTeamChildren($openid);
        // 返回
        return $ids;
    }

    /**
     * 我的团队-接口 [办卡推广下线]
     */
    public function wxgetmyteam(Request $request)
    {
        // 逻辑
        // 验证
        $this->validate($request, [
            'openid' => 'required',
        ]);
        // 逻辑
        $openid = $request->openid;
        // 然后取出下面所有的办卡列表
        $applycards = ApplyCard::where('invite_openid', $openid)->orderBy('created_at', 'desc')->get();
        // 数据整理
        foreach ($applycards as $k => $applycard) {
            if ($applycard->status == '0') {
                $applycards[$k]->status_name = '审核中';
            } else if ($applycard->status == '1') {
                $applycards[$k]->status_name = '审核通过';
            } else {
                $applycards[$k]->status_name = '审核未通过';
            }
            // 申请卡片
            $applycards[$k]->card_name = $applycard->cardbox->merCardName;
            // 姓名隐藏处理
            $applycards[$k]->hide_user_name = $this->substr_cutname($applycard->user_name);
            // 手机号隐藏处理
            $applycards[$k]->hide_user_phone = $this->hidephone($applycard->user_phone);
        }
        // 返回
        return $applycards;
    }

    /**
     * 我的团队-接口 [统计当前用户办卡人数，而不是记录条数，等待完成]
     */
    public function wxgetteamsumagent(Request $request)
    {
        // 逻辑
        // 验证
        $this->validate($request, [
            'openid' => 'required',
        ]);
        // 逻辑
        $openid = $request->openid;
        // 然后取出下面所有的办卡列表，按照申请人分组
        $applycards = DB::table('apply_cards')
            ->select(DB::raw('count(user_name) as sum'), 'user_name')
            ->where('invite_openid', $openid)
            ->groupBy('user_name')
            ->get();
        // 返回
        return $applycards;
    }

    /**
     * 获得一个用户下面的所有下级合伙人
     */
    public function getTeamChildren($openid)
    {
        // 逻辑
        // 定义一个空数组
        $children = [];
        // 判断逻辑
        // 首先寻找下级
        // $agents = Agent::where('parentopenid', $openid)->get();
        $agents = $this->getSonAgents($openid);
        // 返回结果
        if ($agents->count()) {
            // 遍历
            foreach ($agents as $agent) {
                // 把结果压入新数组
                $children[] = $agent->id;
                // 递归继续查找，然后拼接
                $children = array_merge($children, $this->getTeamChildren($agent->openid));
            }
        }
        // 返回结果
        return $children;
    }

    /**
     * 获得一个合伙人下面所有的，同时还包括他自己
     */
    public function getSelfTeamChildren($openid)
    {
        // 首先拿到所有的子类
        $children = $this->getTeamChildren($openid);
        // $id = Agent::where('openid', $openid)->first()->id;
        $id = $this->getAgent($openid)->id;
        // 加上自己
        array_unshift($children, $id);
        // 最终返回
        return $children;
    }

    /**
     * 我的团队-v2接口，采用post传递
     * 根据openid查询，不需要登录
     */
    public function wxMyteamIds(Request $request)
    {
        // 验证
        $this->validate($request, [
            'openid' => 'required',
        ]);
        // 逻辑
        $openid = $request->openid;
        // 首先取出下级所有的合伙人
        $ids = $this->getTeamChildren($openid);
        // 返回
        return $ids;
    }

    /**
     * 我的团队-列表
     */
    public function myteam()
    {
        // 获取授权用户信息
        $user = $this->getauthuser();
        // 渲染
        return view('agent.myteam', compact('user'));
    }

    /**
     * 激励金记录（接口）
     * 根据openid查询，无需登录
     */
    public function getincent(Request $request)
    {
        // 验证
        $this->validate($request, [
            'openid' => 'required|string',
        ]);
        // 判断agent
        $openid = $this->request->openid;
        $agent = $this->getAgent($openid);
        // 查找
        if ($agent) {
            // 分润记录
            $finances = Finance::where('agent_id', $agent->id)->where('status', '1')->orderBy('updated_at', 'desc')->get();
            // 时间格式化
            foreach ($finances as $k => $finance) {
                $finances[$k]->format_created_at = date('Y-m-d', strtotime($finance->created_at));
                // 判断是否系统自动分润，如果没有创建人，那么就是系统分润
                if (empty($finances[$k]->creater)) {
                    $finances[$k]->source = '办卡返佣';
                } else {
                    $finances[$k]->source = $finances[$k]->description;
                }
            }
            $response = [
                'code'  => '0',
                'count' => $finances->count(),
                'data'  => [
                    'agent'    => $agent,
                    'finances' => $finances,
                ],
            ];
        } else {
            $response = [
                'code'  => '1',
                'count' => '0',
                'data'  => null,
            ];
        }
        return $response;
    }


    /**
     * 推荐银行列表 [接口]
     */
    public function cardboxes()
    {
        // 逻辑
        // 转成数组
        // 首先判断是否存在缓存，如果存在，则不重复读取
        if (!Cache::has('cardboxes')) {
            // 写入缓存
            Cache::forever('cardboxes', $this->getCardboxesList());
        }
        // 判断
        if (count(Cache::get('cardboxes'))) {
            $response = [
                'code' => '0',
                'data' => Cache::get('cardboxes'),
            ];
        } else {
            $response = [
                'code' => '1',
                'data' => null,
            ];
        }
        // 返回
        return $response;
    }


    /**
     * 获取cardboxes列表
     */
    public function getCardboxesList()
    {
        // 逻辑
        $cardboxes = Cardbox::select(['id', 'merCardName', 'merCardImg', 'merCardJinduImg', 'littleFlag', 'creditCardUrl', 'creditCardJinduUrl', 'cardAmount', 'rate', 'method', 'merCardOrderImg'])->where('status', '1')->orderBy('sort', 'desc')->get()->toArray();
        // 返回
        return $cardboxes;
    }


    /**
     * 取出cardboxes缓存 【测试】
     */
    public function getCardboxesCache()
    {
        // // 逻辑
        // $this->request->session()->forget('cardboxes');
        // $cardboxes = Cardbox::select(['id', 'merCardName', 'merCardImg', 'merCardJinduImg', 'littleFlag', 'creditCardUrl', 'creditCardJinduUrl', 'cardAmount', 'rate', 'method', 'merCardOrderImg'])->where('status', '1')->orderBy('sort', 'desc')->get()->toArray();
        // // 写入session
        // Session::put('cardboxes', $cardboxes);
        // // 返回
        // return Session::get('cardboxes');

        echo '<pre>';
        print_r(Cache::all());
        echo '</pre>';
    }




    /**
     * 测试授权回调页面
     */
    // public function oauth_callback(Request $request)
    // {
    //     // 逻辑
    //     // 获取 OAuth 授权结果用户信息
    //     $user = $this->app->oauth->setRequest($request)->user();
    //     // 写入session，默认2小时，单位是分钟
    //     if (!Cache::get('wechat.oauth_user')) {
    //         Cache::put('wechat.oauth_user', $user->toArray(), 2*60);
    //     }
    //     $targetUrl = empty($_SESSION['target_url']) ? '/agent/wx/testwxindex' : $_SESSION['target_url'];
    //     // 跳转
    //     header('location:'.$targetUrl);
    // }

    /**
     * 修改登录密码，作废
     */
    // public function changepass()
    // {
    //     // 渲染
    //     $page_title = '修改登录密码';
    //     return view('agent.changepass', compact('page_title'));
    // }

    /**
     * 修改登录密码【逻辑】，作废
     */
    // public function changepassdo(Request $request)
    // {
    //     // 验证
    //     $this->validate($request, [
    //         'password' => 'required|confirmed',
    //         'openid' => 'required',
    //     ]);
    //     // 逻辑
    //     // 不能是123456
    //     if (request('password') == '123456') {
    //         $response = [
    //             'code' => '1',
    //             'msg' => '不能设置为系统预设密码，请重试...',
    //         ];
    //         return $response;
    //     }
    //     // 验证通过后赋值
    //     $password = bcrypt(request('password'));
    //     $openid = request('openid');
    //     $agent = Agent::where('openid', $openid)->first();
    //     // 开始修改
    //     $result = $agent->update('password');
    //     if ($result) {
    //         $response = [
    //             'code' => '0',
    //             'msg' => '登录密码修改成功',
    //         ];
    //     } else {
    //         $response = [
    //             'code' => '1',
    //             'msg' => '登录密码修改失败',
    //         ];
    //     }
    //     // 最终返回
    //     return $response;
    // }

    /**
     * 根据openid请求合伙人信息
     */
    /**
     * 微信-我，接口，根据openid查询
     */
    public function wxpostbyopenid(Request $request)
    {
        // 渲染
        $this->validate($request, [
            'openid' => 'required',
        ]);
        // 逻辑
        $openid = request('openid');
        // $agent = Agent::select(['id', 'name', 'mobile', 'cash_password', 'wx_openid'])->where('openid', $openid)->first();
        $agent = $this->getAgent($openid);
        // 如果没有登录，那么在数据库中肯定找不到openid的记录，需要判断
        if ($agent) {
            $account = AgentAccount::select(['available_money', 'frozen_money', 'cash_money', 'sum_money'])->where('agent_id', $agent->id)->first();
            $data = [
                'agent'   => $agent,
                'account' => $account,
                'code'    => '0',
            ];
        } else {
            $account = null;
            $data = [
                'agent'   => $agent,
                'account' => $account,
                'code'    => '1',
            ];
        }
        // 返回结果
        return $data;
    }


    /**
     * 获得所有的合伙人列表，api接口
     */
    public function getAgents()
    {
        // 首先获得所有合伙人信息
        // 按照注册时间倒叙排列
        $agents = $this->getAgentsCache();
        // 如果openid为NULL，那么就赋值为空
        foreach ($agents as $k => $agent) {
            if ($agent['openid'] == 'null' || $agent['openid'] == 'NULL' || !$agent['openid']) {
                $agents[$k]['openid'] = '';
            }
            // 如果不小心赋值为null，那么就为空
            // 这个赋值为null，和上面的空值区分开来
            if ($agent['parentopenid'] == 'null' || $agent['parentopenid'] == 'NULL') {
                $agents[$k]['parentopenid'] = null;
            }
        }
        return $agents;
    }


    /**
     * 获得当前合伙人模型列表，api接口
     */
    // public function getAgent()
    // {
    //     // 接收数据
    //     $openid = $this->request->openid;
    //     // 取出当前合伙人模型
    //     $agent = Agent::select(['id', 'name', 'mobile', 'openid', 'parentopenid', 'created_at'])->where('openid', $openid)->first()->toArray();
    //     // 如果openid为NULL，那么就赋值为空
    //     if ($agent['openid'] == 'null' || $agent['openid'] == 'NULL' || !$agent['openid']) {
    //         $agents[$k]['openid'] = '';
    //     }
    //     return $agent;
    // }


    /**
     * 合伙人格式化，按照上下级关系来进行分类，整理
     * 暂时设定顶级合伙人为0级
     */
    public function formatagents($result, $openid = null, $level = 0)
    {
        // 首先找到0级的
        $tree = [];
        $repeat_str = '|----';
        // 开始循环
        foreach ($result as $k => $v) {
            // 让空值和NULL值进行精确比较，首先是顶级合伙人
            if ($v['parentopenid'] === $openid) {
                // 取出当前模型
                // 如果openid为空，那么就默认为顶级合伙人，停止向上查找
                if (!$openid) {
                    // 上级合伙人
                    $parent_id = null;
                    $parent_mobile = '无';
                    $parent_name = '无';
                    $hide_parent_mobile = '无';
                    $hide_parent_name = '无';
                    // 上级合伙人的上级
                    $parent_parent_id = null;
                    $parent_parent_openid = null;
                    $parent_parent_mobile = '无';
                    $parent_parent_name = '无';
                    $parent_hide_parent_mobile = '无';
                    $parent_hide_parent_name = '无';
                } else {
                    // 上级合伙人
                    $agent = $this->getAgent($openid);
                    if ($agent) {
                        $parent_id = $agent->id;
                        $parent_mobile = $agent->mobile;
                        $parent_name = empty($agent->name) ? '空' : $agent->name;
                        $hide_parent_mobile = $this->hidephone($agent->mobile);
                        $hide_parent_name = empty($agent->name) || ($agent->name == '') ? '空' : $this->substr_cutname($agent->name);
                    } else {
                        // 如果上级合伙人不存在，那么就不算上下级关系
                        $parent_id = null;
                        $parent_mobile = '无';
                        $parent_name = '无';
                        $hide_parent_mobile = '无';
                        $hide_parent_name = '无';
                    }

                    // 上级合伙人的上级
                    // 如果存在
                    if ($agent->parentopenid && ($agent->parentopenid != 'NULL') && ($agent->parentopenid != 'null')) {
                        $parent_parent_agent = $this->getAgent($agent->parentopenid);
                        if ($parent_parent_agent) {
                            $parent_parent_id = $parent_parent_agent->id;
                            $parent_parent_openid = $agent->parentopenid;
                            $parent_parent_mobile = $parent_parent_agent->mobile;
                            $parent_parent_name = empty($parent_parent_agent->name) ? '****' : $parent_parent_agent->name;
                            $parent_hide_parent_mobile = $this->hidephone($parent_parent_agent->mobile);
                            $parent_hide_parent_name = empty($parent_parent_agent->name) || ($parent_parent_agent->name == '') ? '空' : $this->substr_cutname($parent_parent_agent->name);
                        } else {
                            // 如果有parent_openid，但是数据库没有这个合伙人，那么就不算上下级
                            $parent_parent_id = null;
                            $parent_parent_openid = null;
                            $parent_parent_mobile = '无';
                            $parent_parent_name = '无';
                            $parent_hide_parent_mobile = '无';
                            $parent_hide_parent_name = '无';
                        }
                    } else {
                        $parent_parent_id = null;
                        $parent_parent_openid = null;
                        $parent_parent_mobile = '无';
                        $parent_parent_name = '无';
                        $parent_hide_parent_mobile = '无';
                        $parent_hide_parent_name = '无';
                    }
                }

                $tree[] = [
                    'id'                        => $v['id'],
                    'name'                      => empty($v['name']) ? '****' : $v['name'],
                    'hide_name'                 => empty($v['name']) || ($v['name'] == '****') ? '****' : $this->substr_cutname($v['name']),
                    'mobile'                    => $v['mobile'],
                    'hide_mobile'               => $this->hidephone($v['mobile']),
                    'openid'                    => $v['openid'],
                    'parentopenid'              => $v['parentopenid'],
                    'created_at'                => $v['created_at'],
                    'level'                     => $level,
                    'level_str'                 => str_repeat($repeat_str, $level) . $v['name'],
                    // 上级合伙人
                    'parent_id'                 => $parent_id,
                    'parent_mobile'             => $parent_mobile,
                    'parent_name'               => $parent_name,
                    'hide_parent_mobile'        => $hide_parent_mobile,
                    'hide_parent_name'          => $hide_parent_name,
                    // 上级合伙人的上级
                    'parent_parent_id'          => $parent_parent_id,
                    'parent_parent_openid'      => $parent_parent_openid,
                    'parent_parent_mobile'      => $parent_parent_mobile,
                    'parent_parent_name'        => $parent_parent_name,
                    'parent_hide_parent_mobile' => $parent_hide_parent_mobile,
                    'parent_hide_parent_name'   => $parent_hide_parent_name,
                ];
                // 再去找下面的分级代理
                $tree = array_merge($tree, $this->formatagents($result, $v['openid'], $level + 1));
            }
        }
        // 返回
        return $tree;
    }


    /**
     * 对外显示格式化后的结果-这个是针对全部合伙人的
     */
    public function showtreeagents()
    {
        // 返回
        return $this->formatagents($this->getAgents());
    }

    /**
     * 取出一个合伙人的上级合伙人
     * @param $agent_id
     */
    public function getTop(Request $request, $id)
    {
        // 验证
        $this->validate($request, [
            'id' => 'required',
        ]);
        $id = $request->id;
        // 先从agent表中找到办卡推荐人的模型
        $agent = Agent::find($id);
        if (!$agent) {
            $response = [
                'code' => '1',
                'msg'  => '当前合伙人不存在，上级合伙人也不存在！',
            ];
        } else {
            // 看看parentopenid是否存在，如果不存在，说明没有上级代理人
            if (empty($agent->parentopenid)) {
                $parent_agent = null;
            } else {
                $parent_agent = $this->getParentAgent($agent->parentopenid);
                if (!$parent_agent) {
                    $response = [
                        'code' => '1',
                        'msg'  => '上级合伙人不存在！',
                    ];
                } else {
                    $response = [
                        'code' => '0',
                        'msg'  => '上级合伙人存在',
                        'data' => $parent_agent,
                    ];
                }
            }
        }
        // 最终返回
        return $response;
    }

    /**
     * 对外显示格式化后的结果-这个是针对单个合伙人的
     */
    public function showSingleAgentTree(Request $request)
    {
        // 逻辑
        $openid = request('openid');
        // 再调出所有合伙人的格式化后的表
        $agents = $this->showtreeagents();
        foreach ($agents as $agent) {
            if ($agent['openid'] == $openid) {
                $level = $agent['level'];
            }
        }
        // 返回
        return $this->formatagents($agents, $openid, $level + 1);
    }

    /**
     * 取出授权后用户的基本信息
     */
    public function getauthuser()
    {
        // 逻辑
        $wechat_user = session('wechat.oauth_user.default');
        // 返回
        return $wechat_user;
    }

    /**
     * 办卡须知
     */
    public function wxstrategy()
    {
        // 获取授权用户信息
        $user = $this->getauthuser();
        // 渲染
        return view('agent.strategy', compact('user'));
    }

    /**
     * 申请办卡
     */
    public function wxapplycard()
    {
        // 逻辑
        $bankid = $this->request->bankid;
        $bank = Cardbox::find($bankid);
        // 获取授权用户信息
        $user = $this->getauthuser();
        // 渲染
        return view('agent.applycard', compact('bank', 'user'));
    }

    /**
     * 申请办卡-逻辑 [接口]
     * 里面的推荐人在首页授权的时候已经写入了，所以无需再次写入
     */
    public function wxapplycardstore(Request $request)
    {
        // 验证
        $this->validate($request, [
            'user_openid'   => 'required',
            'card_id'       => 'required|integer',
            'user_name'     => 'required',
            'user_identity' => 'required',
            'user_phone'    => 'required',
        ]);
        // 逻辑
        $user_openid = request('user_openid');
        $card_id = request('card_id');
        $user_name = request('user_name');
        $user_identity = request('user_identity');
        $user_phone = request('user_phone');
        // 卡片模型
        $cardbox = Cardbox::findOrFail($card_id);
        // 合伙人模型
        // $agent = Agent::where('openid', $user_openid)->first();
        $agent = $this->getAgent($user_openid);
        // 如果合伙人不存在，则返回首页重新授权
        if (!$agent) {
            $response = [
                'code' => '1',
                'msg'  => '合伙人不存在，请返回首页重新授权...',
            ];
            return $response;
        }

        // 初始化变量
        $invite_openid = null;
        $top_openid = null;
        $invite_money = '0.00';
        $top_money = '0.00';

        // 上级和上上级推测逻辑
        // 需要判断这个推荐人还有没有上级
        // 如果存在，就复写变量
        if ($agent->parentopenid) {
            $parentAgent = $this->getAgent($agent->parentopenid);
            if ($parentAgent) {
                $invite_openid = $agent->parentopenid;
                $invite_money = $cardbox->cardAmount;
                // 上上级
                // 先判断不为null
                if ($parentAgent->parentopenid) {
                    $topAgent = $this->getAgent($parentAgent->parentopenid);
                    // 如果存在上上级
                    if ($topAgent) {
                        $top_openid = $parentAgent->parentopenid;
                        $top_money = $cardbox->cardTopAmount;
                    }
                }
            }
        }

        // 判断逻辑
        // 采用事务处理机制
        DB::beginTransaction();
        try {
            // 写入申请记录表
            $created_at = date('Y-m-d H:i:s');
            // 但是里面有个逻辑，那就是如果该用户申请的卡片处于待审核状态，那么再次申请时，不写入数据库，也就是数据库中只保留第一次申请的记录
            $applycard_exists = ApplyCard::where('user_openid', $user_openid)->where('card_id', $card_id)->where('status', '0')->first();
            // 如果数据库存在，说明已经之前已经申请了
            if ($applycard_exists) {
                // 申请过了，来个提示
                // 因为申请第二张卡的时候没有佣金了，所以下面的逻辑就不往下执行了，直接返回并跳转
                // 最终返回
                $response = [
                    'code' => '0',
                    // 'msg' => '您之前申请过该卡片，将直接为您跳转，请稍候...',
                    'msg'  => '信息登记成功，即将跳转到银行申请页面，请稍候...',
                ];
                return $response;
            } else {
                // 否则就新纪录一条申请记录
                // 写入订单号
                $order_id = 'CR' . date('YmdHis') . mt_rand(1000, 9999);
                $applycard = ApplyCard::Create(compact('order_id', 'user_openid', 'card_id', 'invite_openid', 'top_openid', 'invite_money', 'top_money'));

                // 如果新增失败就报错
                if (!$applycard->id) {
                    throw new \Exception('新增申请卡片记录失败！');
                }
            }

            // 如果不报错开始往下执行
            // 申请卡片名字
            $card_name = $cardbox->merCardName;
            // 姓名隐藏处理
            $hide_user_name = $this->substr_cutname($user_name);
            // 手机号隐藏处理
            $hide_user_phone = $this->hidephone($user_phone);

            // 如果存在invite_openid，就推送微信模板，否则就不推送
            if (!empty($invite_openid)) {
                // 开始推送推荐成交通知模板
                $this->app->template_message->send([
                    'touser'      => $invite_openid,
                    'template_id' => 'PcGOMAmyFCBqklWpWSVX_0w-70JMwObKHu9TfMDO8JM',
                    // 这里推送当前用户的推广链接
                    'url'         => 'http://hhr.yiopay.com/agent/wx?wxshare=wxshare&appuuid=wx88d48c474331a7f5&parentopenId=' . $invite_openid,
                    'data'        => [
                        'first'    => [
                            'value' => '您的客户' . $hide_user_name . '正在办理' . $card_name . '信用卡，预计返佣金额' . $invite_money . '元，返佣到账以信用卡申请通过为准，请知悉。',
                            'color' => '#173177',
                        ],
                        "keyword1" => [
                            "value" => $hide_user_name,
                            "color" => "#173177",
                        ],
                        "keyword2" => [
                            "value" => $created_at,
                            "color" => "#173177",
                        ],
                        "keyword3" => [
                            "value" => $invite_money . '元',
                            "color" => "#173177",
                        ],
                        "keyword4" => [
                            "value" => $card_name . '【' . $cardbox->littleFlag . '】',
                            "color" => "#173177",
                        ],
                        "remark"   => [
                            "value" => "推荐好友办卡一张最高奖励90元，赶快行动哦！" . PHP_EOL . '点击查看详情',
                            "color" => "#173177",
                        ],
                    ],
                ]);
            }

            // 提交
            DB::commit();

            // 最终返回
            $response = [
                'code' => '0',
                'data' => $applycard,
                'msg'  => '信息登记成功，即将跳转到银行申请页面，请稍候...',
            ];
            return $response;

        } catch (\Exception $e) {
            // 回滚
            DB::rollback();
            $response = [
                'code' => '1',
                'msg'  => $e->getMessage(),
            ];

            // 记录错误信息
            $msg = '';
            $msg .= '订单编号：' . $order_id . PHP_EOL . PHP_EOL;
            $msg .= '<pre>' . PHP_EOL;
            $arr = print_r($request->all(), true);
            $msg .= "$arr";
            $msg .= PHP_EOL;
            // 写入日志
            Log::info($msg);

            // 返回错误信息并记录
            return $response;
        }

    }


    /**
     * 分享办卡生成二维码
     */
    public function wxshare()
    {
        // 获取授权用户信息
        $user = $this->getauthuser();
        // 卡号，如果有就加上
        if ($this->request->bankId) {
            $bankId = $this->request->bankId;
        } else {
            $bankId = null;
        }
        // 渲染
        return view('agent.share', compact('user', 'bankId'));
    }

    /**
     * 判断该用户是否已经办了该卡，暂时没有用到
     * @param array
     */
    public function wxiscardapply()
    {
        // 获得用户和卡片信息
        $openid = $this->request->openid;
        $cardid = $this->request->cardid;
        // 查找是否有记录
        $result = ApplyCard::where('openid', $openid)->where('card_id', $cardid)->first();
        if ($result) {
            $response = [
                'code' => '0',
                'msg'  => '您已经申请了该卡，请不要重复申请',
            ];
        } else {
            $response = [
                'code' => '1',
                'msg'  => '可以正常申请',
            ];
        }
        return $response;
    }

    /**
     * 获取审核中订单列表 [接口]
     * @return array
     */
    public function wxrevieworders(Request $request)
    {
        // 验证
        $this->validate($request, [
            'user_openid' => 'required',
        ]);
        // 逻辑
        $user_openid = request('user_openid');
        $orders = ApplyCard::orderBy('created_at', 'desc')->where('user_openid', $user_openid)->where('status', '0')->get();
        foreach ($orders as $k => $order) {
            $orders[$k]->cardbox = $order->cardbox;
            // 审核人
            $orders[$k]->agent = $order->agent;
        }
        return $orders;
    }

    /**
     * 获取已完成订单列表 [接口]
     * @return array
     */
    public function wxfinishorders(Request $request)
    {
        // 验证
        $this->validate($request, [
            'user_openid' => 'required',
        ]);
        // 逻辑
        $user_openid = request('user_openid');
        $orders = ApplyCard::orderBy('created_at', 'desc')->where('user_openid', $user_openid)->where('status', '>', '0')->get();
        foreach ($orders as $k => $order) {
            $orders[$k]->cardbox = $order->cardbox;
            // 审核人
            $orders[$k]->agent = $order->agent;
            // 审核状态
            if ($order->status == '1') {
                $orders[$k]->status_name = '已通过';
            } else if ($order->status == '2') {
                $orders[$k]->status_name = '未通过';
            } else if ($order->status == '3') {
                $orders[$k]->status_name = '无记录';
            } else {
                $orders[$k]->status_name = '未知状态';
            }
        }
        return $orders;
    }

    /**
     * 处理前端发过来的日志，测试中，暂不可用
     */
    public function wxsavelog()
    {
        // 验证
        $log = $this->request->log;
        return $log;


        // 写入日志
        $date = date('Y-m-d');
        $file = storage_path('logs/frontend-log-' . $date . '.log');
        if (file_put_contents($file, $log . PHP_EOL, FILE_APPEND)) {
            $response = [
                'code' => '0',
                'msg'  => '日志写入成功',
            ];
        } else {
            $response = [
                'code' => '1',
                'msg'  => '日志写入失败',
            ];
        }
        return $response;
    }


    /**
     * 主动发消息给用户
     * api文档：https://www.easywechat.com/docs/master/zh-CN/official-account/customer_service
     * @return bool
     */
    public function wxask(Request $request, $id)
    {
        // 逻辑
        $wechat_message = WechatMessage::findOrFail($id);
        // 提问者用户信息
        $ask_user = json_decode(stripslashes($wechat_message->ask_user));
        // 提问者opneid
        $ask_openid = $wechat_message->ask_openid;
        // 取出用户合伙人的名字
        // $agent = Agent::where('openid', $ask_openid)->first();
        $agent = $this->getAgent($ask_openid);
        // 如果不存在，还是取出留言者用户昵称
        if (!$agent) {
            // 用户前缀标记为游客
            $user_prefix = '游客';
            $user_name = $ask_user->nickname;
        } else {
            // 如果存在，那么就是合伙人
            $user_prefix = '合伙人';
            // 如果存在，但是名字为空，那么取合伙人手机号
            if (!$agent->name) {
                $user_name = $agent->mobile;
            } else {
                $user_name = $agent->name;
            }
        }
        // 渲染
        return view('wechat.edit', compact('wechat_message', 'user_name', 'user_prefix'));
    }


    /**
     * 主动发消息给用户
     * api文档：https://www.easywechat.com/docs/master/zh-CN/official-account/customer_service
     * @return bool
     */
    public function wxanswer(Request $request, $id)
    {
        // 验证
        $this->validate($request, [
            'answer_msg' => 'required',
        ]);
        // 逻辑
        // 回复消息
        $answer_msg = $request->answer_msg;
        // 获取授权用户信息
        $user = $this->getauthuser();
        // 回复者openid
        $answer_openid = $user['id'];
        // 判断回复人是否为管理员
        // 定义了3个管理员
        // 李龙：ol0Z1uLbitksYmYY9IDKfuVsiU1g
        // 刘宗阳：ol0Z1uAO8pkZLapzV3SFJO-msRHg
        // 安学胜：ol0Z1uKKDG7lHEAzwMvf0W21FCgw
        if (($answer_openid != 'ol0Z1uLbitksYmYY9IDKfuVsiU1g') && ($answer_openid != 'ol0Z1uAO8pkZLapzV3SFJO-msRHg') && ($answer_openid != 'ol0Z1uKKDG7lHEAzwMvf0W21FCgw')) {
            $response = [
                'code' => '1',
                'msg'  => '抱歉，非管理员不能回复用户留言！',
            ];
            return $response;
        }
        // 消息模型
        $wechat_message = WechatMessage::findOrFail($id);
        // 首先转发至用户
        $this->app->customer_service->message($answer_msg)->to($wechat_message->ask_openid)->send();
        // 接下来写入数据库
        $result = $wechat_message->update(compact('answer_openid', 'answer_msg'));

        // 记录日志
        // 记录错误信息
        $msg = '';
        $msg .= '数据库新纪录：' . PHP_EOL . PHP_EOL;
        $msg .= '<pre>' . PHP_EOL;
        $arr = print_r($result, true);
        $msg .= "$arr";
        $msg .= PHP_EOL;
        // 写入日志
        Log::info($msg);

        // 返回逻辑
        if ($result) {
            $response = [
                'code' => '0',
                'msg'  => '回复成功',
            ];
        } else {
            $response = [
                'code' => '1',
                'msg'  => '回复失败',
            ];
        }

        // 返回
        return $response;
    }

    /**
     * 判断当前消息是否已经被管理员回复
     * api文档：https://www.easywechat.com/docs/master/zh-CN/official-account/customer_service
     * @return bool
     */
    public function wxcheckisanswer(Request $request, $id)
    {
        // 逻辑
        // 消息模型
        $wechat_message = WechatMessage::findOrFail($id);
        // 判断是否回复
        if (empty($wechat_message->answer_openid) || empty($wechat_message->answer_msg)) {
            $response = [
                'code' => '0',
                'msg'  => '该留言还未被管理员回复',
            ];
        } else {
            $response = [
                'code' => '1',
                'msg'  => '温馨提示：该留言已被管理员回复',
            ];
        }
        // 返回
        return $response;
    }

    /**
     * 检查当前授权的openid用户是否已经注册，如果注册，那么取出这个合伙人模型
     * openid是可以被缓存的哦，这样可以节省资源
     */
    public function wxcheckbyopenid()
    {
        // 接收数据
        $openid = $this->request->openid;
        if (empty($this->request->openid)) {
            $response = [
                'code' => '1',
                'msg'  => 'openid不存在，请先进行微信授权再访问此页面',
                'data' => null,
            ];
            return $response;
        }
        // 取出数据库中查询的结果
        $agent = $this->getAgent($openid);
        // 如果系统里没有这个用户，那么就没有权限请求这个接口
        if (!$agent) {
            $response = [
                'code' => '1',
                'msg'  => '合伙人不存在，请先进行微信授权再访问此页面',
                'data' => null,
            ];
        } else {
            $response = [
                'code' => '0',
                'msg'  => '合伙人存在',
                'data' => $agent,
            ];
        }
        return $response;
    }

    /**
     * 微信消息列表
     */
    public function wechatmsgs()
    {
        // 逻辑
        $ask_openid = $this->request->ask_openid;
        // 取出用户合伙人的名字
        // $agent = Agent::where('openid', $ask_openid)->first();
        $agent = $this->getAgent($ask_openid);
        // 如果不存在，前缀加游客，然后取出留言者的昵称
        if (!$agent) {
            // 游客前缀
            $user_prefix = '游客';
            $user_name = $ask_openid;
        } else {
            // 加合伙人
            $user_prefix = '合伙人';
            // 如果存在，但是名字为空，那么取合伙人手机号
            if (!$agent->name) {
                $user_name = $agent->mobile;
            } else {
                $user_name = $agent->name;
            }
        }
        $lists = WechatMessage::where('ask_openid', $ask_openid)->orderBy('created_at', 'desc')->get();
        // 数据加工
        // 编号，最后发送的排在最上面，也就是最大哦
        $i = count($lists);
        foreach ($lists as $k => $list) {
            // 回复人姓名
            // 如果回复者不为空
            if (!empty($list->answer_openid)) {
                // $answer_user = Agent::where('openid', $list->answer_openid)->first();
                $answer_user = $this->getAgent($list->answer_openid);
                if ($answer_user) {
                    $answer_name = '【' . $answer_user->name . '】';
                } else {
                    $answer_name = '';
                }
            } else {
                $answer_name = '';
            }

            // 提问者信息
            $ask_user = json_decode(stripslashes($list->ask_user), true);
            // 编号
            $lists[$k]->current_id = $i;
            // 提问者信息
            $lists[$k]->ask_user = $ask_user;
            // 前缀
            $lists[$k]->ask_user_prefix = $user_prefix;
            // 提问者名字
            // 如果是ask_openid，那么就取出微信昵称
            if ($user_name == $ask_openid) {
                $lists[$k]->ask_name = $ask_user['nickname'];
            } else {
                $lists[$k]->ask_name = $user_name;
            }

            // 是否回复判断
            if (empty($list->answer_msg)) {
                $lists[$k]->answer_msg_format = '管理员还未回复，点击<a href="' . route('wechat.wxask', ['id' => $list->id]) . '">这里</a>立即回复';
            } else {
                // $lists[$k]->answer_msg_format = '管理员于'.$list->updated_at.'回复：'.$list->answer_msg.'<br>====> 点击<a href="'.route('wechat.wxask', ['id' => $list->id]).'">这里</a>重新回复 <====';
                $lists[$k]->answer_msg_format = '管理员' . $answer_name . '于' . $list->updated_at . '回复：' . $list->answer_msg . '<br>';
            }
            // 每循环一次-1
            $i--;
        }
        // 返回
        return $lists;
    }

    /**
     * 判断当前用户是否进行了实名认证
     */
    public function wxisreal()
    {
        // 逻辑
        $openid = $this->request->openid;
        // 取出用户合伙人模型
        $agent = $this->getAgent($openid);
        // 判断
        // 如果存在，判断三要素是否都填写了
        if ($agent) {
            // 如果没有填写，那么就没有实名认证
            if (empty($agent->name) || empty($agent->id_number) || empty($agent->mobile)) {
                $response = [
                    'code' => '1',
                    'msg'  => '合伙人未实名认证',
                ];
            } else {
                $response = [
                    'code' => '0',
                    'data' => $agent,
                    'msg'  => '已实名认证',
                ];
            }
        } else {
            // 如果不存在，说明还不是合伙人，更加没有实名认证
            $response = [
                'code' => '1',
                'msg'  => '非合伙人未实名认证',
            ];
        }
        // 最终返回
        return $response;
    }

    /**
     * 用户实名认证
     */
    public function wxauthentication()
    {
        // 逻辑
        $openid = $this->request->openid;
        $name = $this->request->openid;
        $id_number = $this->request->id_number;
        $mobile = $this->request->mobile;
        $invite_openid = $this->request->invite_openid;
        // 取出用户合伙人模型
        // $agent = Agent::where('mobile', $mobile)->first();
        $agent = $this->getAgent($openid);
        // 判断
        // 如果存在，判断三要素是否都填写了
        if ($agent) {
            // 进行实名认证
            // 如果三要素有任何一个为空，说明还没有进行实名认证
            if (empty($agent->name) || empty($agent->id_number) || empty($agent->mobile)) {
                if ($agent->update(compact('name', 'id_number', 'mobile'))) {
                    $response = [
                        'code' => '0',
                        'msg'  => '修改合伙人认证成功',
                    ];
                } else {
                    $response = [
                        'code' => '1',
                        'msg'  => '修改合伙人认证失败',
                    ];
                }
            }
        } else {
            // 如果不存在，说明还不是合伙人，更加没有实名认证，首先把该填的都填上
            // 如果邀请人邀请自己注册，那么邀请无效，上级合伙人为空
            if ($openid == $invite_openid) {
                $parentopenid = null;
            } else {
                // 如果是正常点击别人的邀请链接注册，这个时候邀请人肯定是合伙人的，毋庸置疑
                $parentopenid = $invite_openid;
            }
            // 添加新合伙人，也用$agent命名
            $agent = Agent::create([
                'sname'        => $name,
                'name'         => $name,
                'id_number'    => $id_number,
                'wx_openid'    => $openid,
                'openid'       => $openid,
                'parentopenid' => $parentopenid,
                'mobile'       => $mobile,
                // 初始密码为123456
                'password'     => bcrypt('123456'),
                // method为4，实名认证注册
                'method'       => '4',
            ]);
            if ($agent) {
                $response = [
                    'code' => '0',
                    'msg'  => '新增合伙人认证成功',
                ];
            } else {
                $response = [
                    'code' => '1',
                    'msg'  => '新增合伙人认证失败',
                ];
            }
        }

        // 更新当前合伙人缓存
        $this->deleteAgentCache($openid);
        $this->createAgentCache($openid);

        // 最终返回
        return $response;
    }


    /**
     * 微信-信用卡介绍
     */
    public function wxcardinfo()
    {
        // 逻辑
        $bankid = $this->request->bankid;
        $bank = Cardbox::find($bankid);
        // 获取授权用户信息
        $user = $this->getauthuser();
        // 渲染
        return view('agent.cardinfo', compact('bank', 'user', 'bankid'));
    }

    /**
     * 实名身份认证
     */
    public function wxidentityforreal()
    {
        // 逻辑
        // 获取授权用户信息
        $user = $this->getauthuser();
        // 渲染
        return view('agent.identityforreal', compact('user'));
    }

    /**
     * 实名身份认证【逻辑】
     */
    public function wxidentityforrealstore(Request $request)
    {
        // 验证
        // 分两种情况，一是简单注册，二是没有注册
        // 如果是简单注册，那么用户只填写了手机号，剩下的姓名，身份证都没有写，这个时候要提醒用户进行补全操作
        // 如果是没有注册，那么就进行完全写入
        $this->validate($request, [
            // 'openid' => 'required|unique:agents,openid',
            'openid'    => 'required',
            'name'      => 'required|string',
            // 'mobile' => 'required|unique:agents,mobile|regex:/^1[345678][0-9]{9}$/',
            'mobile'    => 'required|regex:/^1[345678][0-9]{9}$/',
            // 'id_number' => 'required|unique:agents,id_number',
            'id_number' => 'required',
        ]);

        // 逻辑
        $openid = $request->openid;
        $wx_openid = $openid;
        $name = $request->name;
        $sname = $name;
        $id_number = $request->id_number;
        $mobile = $request->mobile;
        $parentopenid = $request->parentopenid;
        $invite_openid = $request->parentopenid;
        // 实名认证方式
        $method = '4';
        // 默认密码
        $password = bcrypt('123456');

        // 保存
        // 因为要记录两次，所以这里启用事务处理
        DB::beginTransaction();
        try {
            // 判断身份证号能否被使用
            $checkIdentity = $this->wxcheckidnumbervalid();
            // 如果不可用
            if ($checkIdentity['code'] == '1') {
                throw new \Exception($checkIdentity['msg']);
            }

            // 判断手机号能否使用
            $checkMobile = $this->wxcheckmobilevalid();
            // 如果不可用
            if ($checkMobile['code'] == '1') {
                throw new \Exception($checkMobile['msg']);
            }

            // 如果上面的验证都通过了，就按照openid的值进行更新
            $agent = $this->getAgent($openid);
            // 如果存在，就更新
            if ($agent) {
                // 判断之前是否有保存openid，如果有，那么就不更新
                if (empty($agent->wx_openid) && empty($agent->openid)) {
                    // 如果同时为空，那么就写入openid信息
                    if (!$agent->update(compact('wx_openid', 'openid'))) {
                        throw new \Exception('更新合伙人openid失败');
                    }
                }
                // 更新姓名，身份证，手机号
                if (!$agent->update(compact('name', 'id_number', 'mobile', 'sname'))) {
                    throw new \Exception('更新合伙人姓名、身份证信息失败');
                }
            } else {
                // 否则，就创建
                // 如果邀请人邀请自己注册，那么邀请无效，合伙人上级为空，邀请人为空，佣金为0，上级佣金也为0
                if ($openid == $invite_openid) {
                    $parentopenid = null;
                } else {
                    // 如果是正常点击别人的邀请链接注册，这个时候邀请人肯定是合伙人的，毋庸置疑
                    $parentopenid = $invite_openid;
                }

                // 合伙人模型写入
                $agent = Agent::create(compact('openid', 'wx_openid', 'name', 'id_number', 'mobile', 'parentopenid', 'method', 'sname', 'password'));
                if (!$agent) {
                    throw new \Exception('创建合伙人失败');
                }

                // 写入sid值
                $agent_id = $agent->id;
                $result = \DB::table('agents')->select(\DB::raw("concat('M', right(concat('00000' , id), 5)) as sid"))->where('id', $agent_id)->get();
                $sid = $result[0]->sid;

                // 如果没有sid值，那么就报错
                if (!$sid) {
                    throw new \Exception('生成合伙人ID失败');
                }
                if (!$agent->update(compact('sid'))) {
                    throw new \Exception('更新合伙人ID失败');
                }

                // 如果agentaccount表没有这个用户，那么就新增
                if (!AgentAccount::firstOrCreate(['agent_id' => $agent_id], [
                    'frozen_money'    => '0.00',
                    'available_money' => '0.00',
                    'sum_money'       => '0.00',
                ])) {
                    throw new \Exception('写入用户资产表失败');
                }
            }

            // 如果parentopenid有效，那么就更新下级合伙人缓存
            if ($parentopenid) {
                // 重新生成下级合伙人缓存
                $this->deleteSonAgentsCache($parentopenid);
                $this->createSonAgentsCache($parentopenid);
            }

            // 重新生成当前合伙人缓存
            // 先删除
            $this->deleteAgentCache($openid);
            // 再生成
            $this->createAgentCache($openid);

            // 提交
            DB::commit();

            // 返回
            $response = [
                'code' => '0',
                'data' => $agent,
                'msg'  => '合伙人实名认证成功',
            ];
            return $response;

        } catch (\Exception $e) {
            // 回滚
            DB::rollback();
            // 错误返回
            $response = [
                'code' => '1',
                'msg'  => $e->getMessage(),
            ];
            return $response;
        }
    }


    /**
     * 检测实名认证时是否能使用当前这个手机号
     */
    public function wxcheckmobilevalid()
    {
        // 逻辑
        $openid = $this->request->openid;
        $mobile = $this->request->mobile;
        // 需要查找这个手机号有没有注册
        // 如果没有注册，可以使用；
        // 如果已经注册，看其保存的openid，如果保存的openid和当前接收过来的openid相等，说明用户在修改自己的资料，这个允许，否则不能修改
        $agent = Agent::where('mobile', $mobile)->first();
        if ($agent) {
            if ($agent->openid == $openid) {
                $response = [
                    'code' => '0',
                    'msg'  => '手机号可以使用',
                ];
            } else {
                $response = [
                    'code' => '1',
                    'msg'  => '手机号不可以使用',
                ];
            }
        } else {
            $response = [
                'code' => '0',
                'msg'  => '手机号可以使用',
            ];
        }
        // 最终返回
        return $response;
    }


    /**
     * 检测实名认证时是否能使用当前这个身份证号
     */
    public function wxcheckidnumbervalid()
    {
        // 逻辑
        $openid = $this->request->openid;
        $id_number = $this->request->id_number;
        // 需要查找这个身份证号有没有注册
        // 如果没有注册，可以使用；
        // 如果已经注册，看其保存的openid，如果保存的openid和当前接收过来的openid相等，说明用户在修改自己的资料，这个允许，否则不能修改
        $agent = Agent::where('id_number', $id_number)->first();
        if ($agent) {
            if ($agent->openid == $openid) {
                $response = [
                    'code' => '0',
                    'msg'  => '身份证号可以使用',
                ];
            } else {
                $response = [
                    'code' => '1',
                    'msg'  => '身份证号不可以使用',
                ];
            }
        } else {
            $response = [
                'code' => '0',
                'msg'  => '身份证号可以使用',
            ];
        }
        // 最终返回
        return $response;
    }


    /**
     * 意远平台服务协议
     */
    public function wxagreement()
    {
        // 渲染
        return view('agent.agreement');
    }

    /**
     * 修改合伙人手机号
     */
    public function wxmodifymobile(Request $request)
    {
        // 验证
        $this->validate($request, [
            'openid' => 'required',
            'mobile' => 'required|unique:agents,mobile|regex:/^1[345678][0-9]{9}$/',
        ]);
        // 逻辑
        $openid = $this->request->openid;
        $mobile = $this->request->mobile;
        // 修改
        $agent = $this->getAgent($openid);
        if ($agent) {
            // 如果存在就修改
            if ($agent->update(compact('mobile'))) {
                $response = [
                    'code' => '0',
                    'msg'  => '合伙人手机号修改成功',
                ];
            } else {
                $response = [
                    'code' => '1',
                    'msg'  => '合伙人手机号修改失败',
                ];
            }
        } else {
            $response = [
                'code' => '1',
                'msg'  => '合伙人不存在',
            ];
        }
        // 返回
        return $response;
    }

    /**
     * 验证码过期
     */
    public function removewxyzm()
    {
        // 逻辑
        // 必须通过微信访问才有这个权限
        // 清除验证码
        Cache::forget('wxyzm');
        // 返回
        $response = [
            'code' => '0',
            'msg'  => '缓存清除成功',
        ];
        return $response;
    }


    /**
     * 取出微信公众号参数
     */
    public function getWxConfig()
    {
        // 逻辑
        return $this->config;
    }


    /**
     * 引入微信sdk
     * @param $openid 微信openid 传入构造函数，用作cache缓存
     */
    public function getSignPackage($openid)
    {
        // 引入微信jssdk
        $base_url = base_path('public');
        require_once $base_url . "/backend/wxjssdk/jssdk.php";
        // 这里一定要加\，否则会找不到
        $jssdk = new \JSSDK($this->config['app_id'], $this->config['secret'], $openid);
        return $jssdk->GetSignPackage();
    }

    /**
     * 创建当前游客为合伙人，暂时没用上
     * @param $openid 微信用户openid
     * @method $openid 添加方式
     */
    // public function createWxAgent($openid, $method)
    // {
    //     // 因为要记录两次，所以这里启用事务处理
    //     DB::beginTransaction();
    //     try {
    //         // 逻辑
    //         $agent = Agent::where('openid', $openid)->first();
    //         // 如果不存在，就写入
    //         if (!$agent) {
    //             $wx_openid = $openid;
    //             // 产生新合伙人
    //             // 随机密码123456
    //             $password = bcrypt('123456');
    //             $agent = Agent::create(compact('wx_openid', 'openid', 'method', 'password'));
    //             if (!$agent) {
    //                 throw new \Exception('创建合伙人失败');
    //             }

    //             // 写入sid值
    //             $agent_id = $agent->id;
    //             $result = \DB::table('agents')->select(\DB::raw("concat('M', right(concat('00000' , id), 5)) as sid"))->where('id', $agent_id)->get();
    //             $sid = $result[0]->sid;

    //             // 如果没有sid值，那么就报错
    //             if (!$sid) {
    //                 throw new \Exception('生成合伙人编号失败');
    //             }
    //             if (!$agent->update(compact('sid'))) {
    //                 throw new \Exception('更新合伙人编号失败');
    //             }

    //             // 如果agentaccount表没有这个用户，那么就新增
    //             if (!AgentAccount::firstOrCreate(['agent_id' => $agent_id], [
    //                 'frozen_money' => '0.00',
    //                 'available_money' => '0.00',
    //                 'sum_money' => '0.00',
    //             ])) {
    //                 throw new \Exception('写入合伙人资产表失败');
    //             }

    //             // 提交
    //             DB::commit();

    //             // 成功返回
    //             $response = [
    //                 'code' => '0',
    //                 'msg' => '注册合伙人成功',
    //                 'data' => $agent,
    //             ];
    //             return $response;

    //         }
    //     } catch (\Exception $e) {
    //         // 回滚
    //         DB::rollback();
    //         $response = [
    //             'code' => '1',
    //             'msg' => $e->getMessage(),
    //         ];
    //         return $response;
    //     }
    // }

    /**
     * 从数据库取出当前合伙人
     */
    public function getAgentFromDB($openid)
    {
        // 逻辑
        $agent = Agent::where('openid', $openid)->first();
        // 返回
        return $agent;
    }

    /**
     * 获得当前合伙人模型列表，api接口
     */
    public function getAgent($openid)
    {
        // 取出当前合伙人模型
        if (!Cache::has('agent_' . $openid . '_cache')) {
            $this->createAgentCache($openid);
        }
        // 返回
        return Cache::get('agent_' . $openid . '_cache');
    }

    /**
     * 重新生成合伙人查询缓存
     */
    public function createAgentCache($openid)
    {
        // 逻辑
        Cache::put('agent_' . $openid . '_cache', $this->getAgentFromDB($openid), 120);
    }

    /**
     * 销毁当前合伙人模型
     */
    public function deleteAgentCache($openid)
    {
        // 取出当前合伙人模型
        if (Cache::has('agent_' . $openid . '_cache')) {
            Cache::forget('agent_' . $openid . '_cache');
        }
    }

    /**
     * 重新生成父类合伙人查询缓存
     */
    public function createParentAgentCache($openid)
    {
        // 逻辑
        $agent = Agent::where('parentopenid', $openid)->first();
        // 返回
        return $agent;
    }

    /**
     * 获得当前父类合伙人模型列表，api接口
     */
    public function getParentAgent($openid)
    {
        // 取出当前合伙人模型
        if (!Cache::has('parentagent_' . $openid . '_cache')) {
            Cache::put('parentagent_' . $openid . '_cache', $this->createParentAgentCache($openid), 120);
        }
        // 返回
        return Cache::get('parentagent_' . $openid . '_cache');
    }


    /**
     * 从数据库取出下级合伙人
     */
    public function getSonAgentsFromDB($openid)
    {
        // 逻辑
        $agents = Agent::where('parentopenid', $openid)->get();

        // 返回
        return $agents;
    }

    /**
     * 获得当前下级合伙人查询缓存，api接口
     */
    public function getSonAgents($openid)
    {
        // 取出当前合伙人模型
        if (!Cache::has('sonagents_' . $openid . '_cache')) {
            $this->createSonAgentsCache($openid);
        }
        // 返回
        return Cache::get('sonagents_' . $openid . '_cache');
    }

    /**
     * 重新生成下级合伙人查询缓存
     */
    public function createSonAgentsCache($openid)
    {
        // 逻辑
        Cache::put('sonagents_' . $openid . '_cache', $this->getSonAgentsFromDB($openid), 120);
    }

    /**
     * 销毁下级合伙人查询缓存
     */
    public function deleteSonAgentsCache($openid)
    {
        // 如果存在则销毁
        if (Cache::has('sonagents_' . $openid . '_cache')) {
            Cache::forget('sonagents_' . $openid . '_cache');
        }
    }

    /**
     * 从数据库取出代付通道
     */
    public function getAdvanceMethodsFromDB()
    {
        // 逻辑
        $method = AdvanceMethod::where('status', '1')->first();
        // 返回
        return $method;
    }

    /**
     * 取出代付通道缓存
     */
    public function getAdvanceMethod()
    {
        // 取出当前合伙人模型
        if (!Cache::has('advance_method')) {
            $this->createAdvanceMethodCache();
        }
        // 返回
        return Cache::get('advance_method');
    }

    /**
     * 重新生成下级合伙人查询缓存
     */
    public function createAdvanceMethodCache()
    {
        // 逻辑
        Cache::forever('advance_method', $this->getAdvanceMethodsFromDB());
    }

    /**
     * 销毁代付通道缓存
     */
    public function deleteAdvanceMethodCache()
    {
        // 取出当前合伙人模型
        if (Cache::has('advance_method')) {
            Cache::forget('advance_method');
        }
    }

    /**
     * 从数据库取出全部合伙人
     */
    public function getAgentsFromDB()
    {
        // 逻辑
        $agents = Agent::select(['id', 'name', 'mobile', 'openid', 'parentopenid', 'created_at'])->orderBy('created_at', 'desc')->get()->toArray();
        // 返回
        return $agents;
    }

    /**
     * 取出代付通道缓存
     */
    public function getAgentsCache()
    {
        // 取出当前合伙人模型
        if (!Cache::has('agents')) {
            $this->createAgentsCache();
        }
        // 返回
        return Cache::get('agents');
    }

    /**
     * 重新生成全部合伙人模型
     */
    public function createAgentsCache()
    {
        // 逻辑
        Cache::put('agents', $this->getAgentsFromDB(), 120);
    }

    /**
     * 销毁合伙人缓存，做过期处理
     */
    public function deleteAgentsCache()
    {
        // 如果存在则销毁
        if (Cache::has('agents')) {
            Cache::forget('agents');
        }
    }

}
