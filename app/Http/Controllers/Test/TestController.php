<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Agent\AgentauthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WeChatController;
use App\Jobs\SendMail;
use App\Models\AdvanceMethod;
use App\Models\Agent;
use App\Models\AgentAccount;
use App\Models\ApplyCard;
use App\Models\Bank;
use App\Models\Cardbox;
use App\Models\Finance;
use App\Models\Freeze;
use App\Models\Role;
use Cache;
use EasyWeChat\Factory;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Log;
use Redis;
use Session;
use Symfony\Component\Cache\Simple\RedisCache;
use Webpatser\Uuid\Uuid;
use Zhuzhichao\BankCardInfo\BankCard;

// 引入微信服务器
// 队列

class TestController extends Controller
{
    // 属性
    protected $result;
    protected $request;
    protected $agentauth;
    // 配置参数
    protected $config;

    // 短信key
    const MSG_APPKEY = '276f2e741f4ec70285a4de40ad378247';

    // 构造函数
    public function __construct(Request $request, AgentauthController $agentauth)
    {
        // 全局配置
        $config = Config::get("wechat.official_account.default");
        // $config_work = Config::get("wechat.work.default");

        // 使用配置来初始化一个公众号应用实例
        $this->app = Factory::officialAccount($config);
        // $this->appwork = Factory::work($config_work);

        // redis缓存
        $predis = app('redis')->connection()->client();

        // 创建缓存实例
        $cache = new RedisCache($predis);

        // 替换应用中的缓存，可以正常工作
        $this->cache = $cache;

        // 全局request
        $this->request = $request;

        // 微信agent操作类
        $this->agentauth = $agentauth;

        // 取出配置
        $this->config = config('wechat.official_account.default');
    }

    /**
     * index首页
     */
    public function index()
    {
        // echo '<pre>';
        // echo Redis::get('name');
        // print_r($request->session());
        return view('test.index');
    }

    /**
     * kindeditor编辑器测试
     */
    public function kindeditor(Request $request)
    {
        // echo '<pre>';
        // echo Redis::get('name');
        // print_r($request->session());
        return view('test.kindeditor');
    }

    /**
     * markdown编辑器测试
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function md(Request $request)
    {
        // echo '<pre>';
        // echo Redis::get('name');
        // print_r($request->session());
        return view('test.md');
    }

    /**
     * redis测试
     */
    public function redis()
    {
        // 测试报文记录
        echo '<pre>';
        echo '当前合伙人的主要信息：' . PHP_EOL;
        print_r(Cache::get('agent_ol0Z1uAO8pkZLapzV3SFJO-msRHg_cache'));
        echo PHP_EOL . PHP_EOL;
        echo '当前结算通道信息' . PHP_EOL;
        print_r(Cache::get('advance_method'));
        echo PHP_EOL . PHP_EOL;
        echo '当前下级合伙人查询缓存' . PHP_EOL;
        print_r(Cache::get('sonagents_ol0Z1uAO8pkZLapzV3SFJO-msRHg_cache'));
        echo PHP_EOL . PHP_EOL;
        echo '所有合伙人：' . PHP_EOL;
        print_r(Cache::get('agents'));
        echo '</pre>';
        // exit();
        // echo \Redis::get('name');

        // 测试
        // Cache::forget('jsapi_ticket');
        // Cache::forget('access_token');

        // echo 'jsapi_ticket='.\Cache::get('jsapi_ticket');
        // echo '<br>';
        // echo 'access_token='.\Cache::get('access_token');
    }

    /**
     * ueditor编辑器测试
     */
    public function ueditor()
    {
        return view('test.ueditor');
    }

    /**
     * session-redis写入测试
     */
    public function setsession(Request $request)
    {
        $request->session()->put('name', 'liuzongyang');
    }

    /**
     * session-redis读取测试
     */
    public function getsession(Request $request)
    {
        echo $request->session()->get('name');
    }

    /**
     * 获取当前控制器和方法
     */
    public function getca()
    {
        echo '<pre>';
        print_r($this->getControllerAction());
        exit();
    }

    /**
     * 获取当前登录用户
     */
    public function authuser()
    {
        echo '<pre>';
        dd(\Auth::user());
        exit();
    }

    /**
     * 获取scout的搜索结果
     */
    public function scout()
    {
        $query = '银行';
        $banks = Bank::search($query)->paginate(2);
        return view('admin.system.bank.index', compact('banks'));
    }

    /**
     * 获取特定密码
     */
    public function passwd()
    {
        return bcrypt('123456');
    }

    /**
     * 测试SID值
     */
    public function testsid()
    {
        // 测试sid值
        $sql = "SELECT concat('M', right(concat('00000' , id), 5)) as sid from agents";
        $result = \DB::select($sql);

        echo '<pre>';
        print_r($result);
        exit;
    }

    /**
     * 获取特殊SID值
     */
    public function getsid()
    {
        // $newid需要经过计算添加到sid字段中
        $newid = 11;
        $sql = "SELECT RIGHT(concat('M0000', id), 6) as sid from agents where id = " . $newid;
        $result = \DB::select($sql);

        echo '<pre>';
        print_r($result);
        exit;
    }

    /**
     * 复杂sql语句改用DB类查询，里面的原生用DB::raw写入
     */
    public function getagent()
    {
        $newid = 11;
        $result = \DB::table('agents')->select(\DB::raw("RIGHT(concat('M', '0000', id), 5) as sid"))->where('id', $newid)->get();
        echo '<pre>';
        print_r($result);
        echo '<br>';
        echo $result[0]->sid;
        exit;
    }

    /**
     * lavavel update
     */
    public function update()
    {
        $name = '2';
        echo '<pre>';
        $status = Bank::find(27)->update(compact('name'));
        echo $status;
    }

    /**
     * 找出一条记录
     */
    public function getone()
    {
        $mobile = '13672066886';
        $status = Agent::select('id')->where('mobile', $mobile)->first();
        echo '<pre>';
        // print_r($status);
        echo '<br><br>';
        echo $status->id;
        // echo $status;
    }

    /**
     * 当前登录用户拥有的所有权限
     */
    public function permissions()
    {
        $id = \Session::get('admin')['admin_id'];
        $roles = Agent::find($id)->roles->pluck('id');

        $role = Role::find($roles[0])->permissions;
        $role_id = ['2', '3'];

        // $permissions = Role::pluck($roles->id)->permissions;
        // $permissions = $roles->permissions()->count();

        echo '<pre>';
        // echo $roles->id;
        print_r($role);
        exit();
    }

    /**
     * token
     */
    public function token()
    {
        $token = Uuid::generate();
        echo $token;
    }

    /**
     * token
     */
    public function logininfo()
    {
        $admin = \Session::get('admin');
        echo '<pre>';
        print_r($admin);
        exit();
    }

    /**
     * 判断记录是否存在
     */
    public function ifexists()
    {
        echo \App\Models\Manager::find(['id' => 1])->count();
        if (\App\Models\Manager::find(['id' => 1])) {
            echo '找到了';
        } else {
            echo '没有找到';
        }
    }

    /**
     * 取出上级合伙人
     */
    public function getparent($id)
    {
        return Agent::find($id)->parent;
    }

    /**
     * orm测试
     */
    public function orm()
    {
        $model = \App\Models\AgentAccount::where('agent_id', 3)->toSql();

        echo $model;
        exit;

        echo \App\Models\AgentAccount::where('agent_id', 3)->count();
        echo '<br >';
        if (\App\Models\AgentAccount::where('agent_id', 3)->first()) {
            echo $model->agent_id;
        } else {
            echo '没有找到';
        }
    }

    /**
     * error json
     */
    public function error()
    {
        throw new AdminException('添加失败');
    }

    /**
     * 删除文件
     */
    public function delete()
    {
        $path = 'storage/20180721/2J3yz7owZLyrO72svfV8ObKxlsBsNMb0ITIWFeul.xls';
        @unlink($path);
    }

    /**
     * 银行卡信息
     */
    public function card()
    {
        $id = $this->request->id;
        echo '<pre>';
        print_r(BankCard::info($id));

        // Array
        // (
        //     [validated] => 1
        //     [bank] => CEB
        //     [bankName] => 中国光大银行
        //     [bankImg] => https://apimg.alipay.com/combo.png?d=cashier&t=CEB
        //     [cardType] => CC
        //     [cardTypeName] => 信用卡
        // )

        // DC: "储蓄卡",
        // CC: "信用卡",
        // SCC: "准贷记卡",
        // PC: "预付费卡"

        exit();
    }

    /**
     * url get测试
     */
    public function getstatus(Request $request)
    {
        $status = $request->get('status');
        if (isset($status)) {
            // echo $request->get('status');
            var_dump($status);
        } else {
            echo '没有值';
            var_dump($status);
        }
    }

    /**
     * get cashid
     */
    public function getcashid()
    {
        // 交易流水号
        $agent_id = 1;
        $agent = Agent::find($agent_id);
        // $cash_id = $agent->sid . '-' . 'rrrr'. time().mt_rand(1000, 9999) .'xxxx';
        $cash_id = 'DF' . date('YmdHis') . mt_rand(1000, 9999);
        return $cash_id; // M00001-rrrr15330068063299xxxx
    }


    /**测试接口 */
    public function testapi()
    {

        // $sql = "update withdraws set status = 1 where id = 78";
        // var_dump(\DB::update($sql));

        // exit();

        // 代付通道
        $method = AdvanceMethod::where('status', '0')->first();
        // 通道ID
        $method_id = $method->id;
        // 通道登录用户名
        $username = $method->username;
        // 通道登录密码
        $password = $method->password;
        // 通道商户代码
        $merchant_id = $method->merchant_id;

        // 引入三个类文件
        $base_url = base_path('public');
        // 测试
        // require_once $base_url . "/backend/allinpayInter/libs/PhpToolsTest.class.php";
        // 正式
        require_once $base_url . "/backend/allinpayInter/libs/ArrayXml.class.php";
        require_once $base_url . "/backend/allinpayInter/libs/cURL.class.php";
        if ($method_id == '1') {
            require_once $base_url . "/backend/allinpayInter/libs/PhpTools.class.php";
        } elseif ($method_id == '2') {
            require_once $base_url . "/backend/allinpayInter/libs/PhpToolsTest.class.php";
        }
        // $tools = new \PhpTools();
        $tools = \PhpTools::getInstance();

        // 源数组
        $params = [
            'INFO'      => [
                'TRX_CODE'  => '200004',
                'VERSION'   => '03',
                'DATA_TYPE' => '2',
                'LEVEL'     => '6',
                'USER_NAME' => "$method->username",
                'USER_PASS' => "$method->password",
                'REQ_SN'    => "DF201808161719478501",
            ],
            'QTRANSREQ' => [
                'QUERY_SN'    => 'DF201808161719478501',
                'MERCHANT_ID' => "$method->merchant_id",
                'STATUS'      => '2',
                'TYPE'        => '1',
                'START_DAY'   => '',
                'END_DAY'     => '',
            ],
        ];

        // 发送请求
        $result = $tools->send($params);
        if ($result != false) {
            echo '验签通过，请对返回信息进行处理';
            //下面商户自定义处理逻辑，此处返回一个数组
        } else {
            print_r("验签结果：验签失败，请检查通联公钥证书是否正确");
        }

        echo '<pre>';
        print_r($result);
        exit();

        // 成功输出结果：
        // Array
        // (
        //     [AIPG] => Array
        //         (
        //             [INFO] => Array
        //                 (
        //                     [TRX_CODE] => 200004
        //                     [VERSION] => 03
        //                     [DATA_TYPE] => 2
        //                     [REQ_SN] => DF15331170723584
        //                     [RET_CODE] => 0000
        //                     [ERR_MSG] => 处理完成
        //                 )

        //             [QTRANSRSP] => Array
        //                 (
        //                     [QTDETAIL] => Array
        //                         (
        //                             [BATCHID] => DF15331170723584
        //                             [SN] => 0
        //                             [TRXDIR] => 0
        //                             [SETTDAY] => 20180801
        //                             [FINTIME] => 20180801175113
        //                             [SUBMITTIME] => 20180801175112
        //                             [ACCOUNT_NO] => 6214862260787777
        //                             [ACCOUNT_NAME] => 李龙
        //                             [AMOUNT] => 150
        //                             [CUST_USERID] => M00001
        //                             [REMARK] => 合伙人M00001于2018-08-01 17:51:12申请提现1.5元
        //                             [SUMMARY] => 提现
        //                             [RET_CODE] => 0000
        //                             [ERR_MSG] => 处理成功
        //                         )

        //                 )

        //         )

        // )


        // Array
        // (
        //     [AIPG] => Array
        //         (
        //             [INFO] => Array
        //                 (
        //                     [TRX_CODE] => 200004
        //                     [VERSION] => 03
        //                     [DATA_TYPE] => 2
        //                     [REQ_SN] => DF201808201122389619
        //                     [RET_CODE] => 0000
        //                     [ERR_MSG] => 处理完成@CChS
        //                 )

        //             [QTRANSRSP] => Array
        //                 (
        //                     [QTDETAIL] => Array
        //                         (
        //                             [BATCHID] => DF201808201122389619
        //                             [SN] => 0
        //                             [TRXDIR] => 0
        //                             [SETTDAY] => 20180820
        //                             [FINTIME] => 20180820112812
        //                             [SUBMITTIME] => 20180820112311
        //                             [ACCOUNT_NO] => 6222020302078888888
        //                             [ACCOUNT_NAME] => 刘宗阳测试
        //                             [AMOUNT] => 111
        //                             [REMARK] => 合伙人M00002于2018-08-20 11:22:38申请提现1.11元
        //                             [SUMMARY] => 提现
        //                             [RET_CODE] => 3999
        //                             [ERR_MSG] => 您输入的卡号无效，详询发卡行[1020114]
        //                         )

        //                 )

        //         )

        // )


        // 失败输出结果：
        // Array
        // (
        //     [AIPG] => Array
        //         (
        //             [INFO] => Array
        //                 (
        //                     [TRX_CODE] => 200004
        //                     [VERSION] => 03
        //                     [DATA_TYPE] => 2
        //                     [REQ_SN] => DF15331148982033
        //                     [RET_CODE] => 0000
        //                     [ERR_MSG] => 处理完成
        //                 )

        //             [QTRANSRSP] => Array
        //                 (
        //                     [QTDETAIL] => Array
        //                         (
        //                             [BATCHID] => DF15331148982033
        //                             [SN] => 0
        //                             [TRXDIR] => 0
        //                             [SETTDAY] => 20180801
        //                             [FINTIME] => 20180801171709
        //                             [SUBMITTIME] => 20180801171503
        //                             [ACCOUNT_NO] => 6222020302074454002
        //                             [ACCOUNT_NAME] => 张二
        //                             [AMOUNT] => 100
        //                             [CUST_USERID] => M00002
        //                             [REMARK] => 合伙人M00002于2018-08-01 17:14:58申请提现1元
        //                             [SUMMARY] => 提现
        //                             [RET_CODE] => 3030
        //                             [ERR_MSG] => 账号错误
        //                         )

        //                 )

        //         )

        // )


        // Array
        // (
        //     [AIPG] => Array
        //         (
        //             [INFO] => Array
        //                 (
        //                     [TRX_CODE] => 200004
        //                     [VERSION] => 03
        //                     [DATA_TYPE] => 2
        //                     [REQ_SN] => DF201808161719478501
        //                     [RET_CODE] => 1002
        //                     [ERR_MSG] => 无此交易
        //                 )

        //         )

        // )


        // Array
        // (
        //     [AIPG] => Array
        //         (
        //             [INFO] => Array
        //                 (
        //                     [TRX_CODE] => 200004
        //                     [VERSION] => 03
        //                     [DATA_TYPE] => 2
        //                     [REQ_SN] => DF201808161719478501
        //                     [RET_CODE] => 1000
        //                     [ERR_MSG] => 用户或密码错误
        //                 )

        //         )

        // )


    }

    // 查看冻结记录
    public function freezes()
    {
        $agent_id = 1;
        $freezes = Freeze::where('agent_id', $agent_id)->get();
        echo '<pre>';
        print_r($freezes);
        if ($freezes->count()) {
            echo '有';
        } else {
            echo '没有';
        }
        exit();
    }

    /**
     * 测试微信服务器
     */
    public function weixin()
    {

        // // redis缓存
        // $predis = app('redis')->connection('default')->client();

        // // 创建缓存实例
        // $cache = new RedisCache($predis);

        // // echo '<pre>';
        // print_r($cache->get('wechat.oauth_user'));
        // echo 'ok';
        // print_r(json_decode($cache->get('wechat.oauth_user'), true));
        // exit();

        $weixin = new WeChatController();
        // $list = $app->menu->list();
        // echo '<pre>';
        // print_r($list);

        exit();


        echo '<pre>';

        // 获得所有的模板列表
        $templates = $weixin->getTemplates();
        // $list = $weixin->menu->list();
        // 发送消息
        // print_r($weixin->sendTemplateMsg('oXZlm0qyX_-o1y0xJrLlaLl-imnQ', 'KkXYk_HKCaPq7TKq974-HOIJYDe3H92V_kOIrWsBbM4'));
        print_r($templates);

        // Array
        // (
        //     [errcode] => 0
        //     [errmsg] => ok
        //     [msgid] => 413094258657280000
        // )

        exit();
    }

    /**
     * 测试微信登录
     */
    public function getconfig()
    {
        // 读取配置
        $config = config('wechat.official_account.default');
        echo '<pre>';
        print_r($config);
        exit();

        return view('agent.wxlogin');
    }

    /**
     * 获取微信openid
     */
    public function getOpenId()
    {
        $appid = 'wx4e62a1d54222f9b2';
        //这里的地址需要http://
        $redirect_uri = urlencode("http://jxc.liuzongyang.com/returnopenid");
        // 跳转地址
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $appid . "&redirect_uri=" . $redirect_uri . "&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
        header('location:' . $url);
    }

    public function returnOpenId()
    {
        $code = $_GET['code'];//获取code
        $appid = 'wx4e62a1d54222f9b2';
        $secret = '487be29d1931c2439e52aa96e93c9756';
        // 通过code换取网页授权access_token
        $weixin = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid . "&secret=" . $secret . "&code=" . $code . "&grant_type=authorization_code");
        $jsondecode = json_decode($weixin); //对JSON格式的字符串进行编码

        // echo '<pre>';
        // print_r($jsondecode);

        // 如果正确显示
        // stdClass Object
        // (
        //     [access_token] => 12_zPtGopLSf32qWp-t8Km8tZQhAJ8oWXw7ih-BCKIN769wTB0RjHEc2ltVdCHuYS8yOSeY2Xa2PYnqe-7d8dMDbg
        //     [expires_in] => 7200
        //     [refresh_token] => 12_PZsM0yI6rMzCwBsCMDUZpWAsjrm3T7Z8WZ5n4zgfkfspj37HINJTexG8vsoAUsf2WGePCc5qqUIK1iVD4uyeIQ
        //     [openid] => oXZlm0qyX_-o1y0xJrLlaLl-imnQ
        //     [scope] => snsapi_base
        // )

        return $jsondecode->openid;

    }

    /**
     * getSessionId
     */
    public function getSessionId(Request $request)
    {

        // redis缓存
        $predis = app('redis')->connection()->client();
        // 创建缓存实例
        $cache = new RedisCache($predis);

        echo '<pre>';
        // print_r(\Session::get('agent'));
        print_r($cache->get('agent'));
        exit();

        $user = [
            'name' => 'liuzongyang',
            'age'  => 30,
            'sex'  => '男',
        ];
        session(['wx' => json_encode($user)]);
        $data = $request->session()->all();
        echo '<pre>';
        $user = session('wx');
        $agent = \Session::get('wx');
        // print_r($data);
        print_r($user);
        echo '<br>';
        print_r($agent);
        exit();
    }

    public function sendMsg()
    {


        // 如果是微信提交的，那么需要把消息通过微信推送给用户
        // 提交成功模板
        // 需要获得关注用户的openid
        // 去数据库中查找agent表中的openid字段是否为空，然后继续
        $cacheObj = json_decode($this->cache->get('wechat.oauth_user'));
        $openid = $cacheObj->openid;
        if (DB::table('agents')->where('wx_openid', $openid)->first()) {
            $this->app->template_message->send([
                'touser'      => $openid,
                'template_id' => 'nXh_FRtLazHNeDBssYkvbSEGLY-5Nh8HND1FhaMwfXc',
                'url'         => 'http://jxc.liuzongyang.com',
                'data'        => [
                    'first'    => [
                        'value' => '您好，您已申请提现成功。',
                        'color' => '#173177',
                    ],
                    "keyword1" => [
                        "value" => '中国银行',
                        "color" => "#173177",
                    ],
                    "keyword2" => [
                        "value" => '6222020302074454521',
                        "color" => "#173177",
                    ],
                    "keyword3" => [
                        "value" => '测试名字',
                        "color" => "#173177",
                    ],
                    "keyword4" => [
                        "value" => '1.11元',
                        "color" => "#173177",
                    ],
                    "keyword5" => [
                        "value" => '2018-05-12 08:08:08',
                        "color" => "#173177",
                    ],
                    "remark"   => [
                        "value" => "本次提现预计2个工作日内到达您指定银行账户，请注意查询！",
                        "color" => "#173177",
                    ],
                ],
            ]);
        }
    }

    // 测试继承
    public function recurrence(Request $request)
    {


        echo '<pre>';
        print_r($this->app->template_message->getPrivateTemplates());
        exit();

        // $agent = Agent::select(['id', 'name', 'mobile', 'cash_password', 'wx_openid'])->where('id', 2)->first();


        $agent = Agent::select(['id', 'name', 'mobile', 'cash_password', 'wx_openid'])->where('id', 2)->first();
        if ($agent) {
            $account = AgentAccount::select(['available_money', 'frozen_money', 'cash_money', 'sum_money'])->where('agent_id', $agent->id)->first();
        } else {
            $account = null;
        }
        $account = AgentAccount::select(['available_money', 'frozen_money', 'cash_money', 'sum_money'])->where('agent_id', $agent->id)->first();
        // 代付通道
        $method = AdvanceMethod::select(['per_charge'])->where('status', '1')->first();
        // 返回
        $data = [
            'agent'   => $agent,
            'account' => $account,
            'method'  => $method,
        ];
        return $data;


        echo '<pre>';
        print_r($data);
        // echo '<br><br>';
        // echo '对象：'.$agent->id;
        // echo '数组：'.$agent['id'];
        exit();


        // agent: {
        //     id: 2,
        //     name: "张海利",
        //     mobile: "18920851999",
        //     cash_password: null,
        //     wx_openid: "ol0Z1uHZwKzQTWHxU7atJlQwYhNY"
        //     },
        // account: {
        //     available_money: 6666,
        //     frozen_money: 0,
        //     cash_money: 0,
        //     sum_money: 6666
        // },
        // method: {
        //     per_charge: 2
        //     }
        // }


        // \Log::info('我就是做个测试');
        // exit();

        echo '<pre>';
        print_r(\Session::get('agent'));
        // print_r($request->session()->get('admin'));
        print_r(\Session::get('admin'));
        exit();

        $old_cash_password = '123456';
        $username = '13672066886';
        $new_cash_password = Agent::find(2)->cash_password;
        if (\Hash::check($old_cash_password, $new_cash_password)) {
            echo '密码吻合';
        } else {
            echo '密码不符合';
        }

        exit();

        if ($hash == $cash_password) {
            echo '密码吻合';
        } else {
            echo '密码不符合' . $hash;
        }

        $card = '6222020302074454521';
        echo $this->substr_cut($card);
        exit();

        $obj = new TestController(12);
        echo $obj->result;
        echo '<br>';

        $a = self::__construct(44);
        //    echo $obj->result;

    }


    //将用户名进行处理，中间用星号表示
    public function substr_cut($user_name)
    {
        //获取字符串长度
        $strlen = mb_strlen($user_name, 'utf-8');
        //如果字符创长度小于2，不做任何处理
        if ($strlen < 2) {
            return $user_name;
        } else {
            //mb_substr — 获取字符串的部分
            $firstStr = mb_substr($user_name, 0, 4, 'utf-8');
            $lastStr = mb_substr($user_name, -4, 4, 'utf-8');
            //str_repeat — 重复一个字符串
            return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 4) : $firstStr . str_repeat("*", $strlen - 8) . $lastStr;
        }
    }

    /**
     * 获得微信的自定义菜单
     */
    public function getwechatmenus()
    {
        // 返回数据
        return $this->app->menu->current();
        // 数据格式如下：
        // Array
        // (
        //     [is_menu_open] => 1
        //     [selfmenu_info] => Array
        //         (
        //             [button] => Array
        //                 (
        //                     [0] => Array
        //                         (
        //                             [type] => view
        //                             [name] => 账户提现
        //                             [url] => http://hhr.yiopay.com/agent/wx
        //                         )

        //                 )

        //         )

        // )
    }


    /**
     * 获得微信的模板列表
     */
    public function getwechattemplates()
    {
        return $this->app->template_message->getPrivateTemplates();
        // Array
        // (
        //     [template_list] => Array
        //         (
        //             [0] => Array
        //                 (
        //                     [template_id] => hRTrbQKHmFdqPJlXemeciEHLSPSjxrFoMu-XpMYO1kc
        //                     [title] => 订阅模板消息
        //                     [primary_industry] =>
        //                     [deputy_industry] =>
        //                     [content] => {{content.DATA}}
        //                     [example] =>
        //                 )

        //             [1] => Array
        //                 (
        //                     [template_id] => hcXQLL9XChHYJYaGNDQgI0G60G8aKh__YuPMNnsI_Xk
        //                     [title] => 监控结果通知
        //                     [primary_industry] => IT科技
        //                     [deputy_industry] => IT软件与服务
        //                     [content] => {{first.DATA}}
        //                                     警告标题：{{keyword1.DATA}}
        //                                     触发时间：{{keyword2.DATA}}
        //                                     警告摘要：{{keyword3.DATA}}
        //                                     {{remark.DATA}}
        //                     [example] => 张三，“Test”发生了警告信息，请及时关注。
        //                                     警告标题：WEB服务器发生异常
        //                                     触发时间：2017.10.08  12:12:12
        //                                     警告摘要：服务已停止运行，请尽快处理。
        //                                     节点”win-385CSTY“当前的状态：已停止运行
        //                 )

        //             [2] => Array
        //                 (
        //                     [template_id] => itwKOvlLf5xRW2YnUkobBEPPWgFuUq6Ju-Way_Y84TA
        //                     [title] => 提现失败提醒
        //                     [primary_industry] => IT科技
        //                     [deputy_industry] => IT软件与服务
        //                     [content] => {{first.DATA}}
        //                                 提现金额：{{keyword1.DATA}}
        //                                 提现时间：{{keyword2.DATA}}
        //                                 失败原因：{{keyword3.DATA}}
        //                                 {{remark.DATA}}
        //                     [example] => 张三您好，您在我微信号的提现失败了
        //                                 提现金额：20元
        //                                 提现时间：2017年1月5日 18:18
        //                                 失败原因：输入的名称不正确
        //                                 请您按照失败原因修改相关信息后，重新提现！
        //                 )

        //             [3] => Array
        //                 (
        //                     [template_id] => nXh_FRtLazHNeDBssYkvbSEGLY-5Nh8HND1FhaMwfXc
        //                     [title] => 提现成功通知
        //                     [primary_industry] => IT科技
        //                     [deputy_industry] => IT软件与服务
        //                     [content] => {{first.DATA}}
        //                                     提现银行：{{keyword1.DATA}}
        //                                     银行卡号：{{keyword2.DATA}}
        //                                     开户人：{{keyword3.DATA}}
        //                                     提现金额：{{keyword4.DATA}}
        //                                     时间：{{keyword5.DATA}}
        //                                     {{remark.DATA}}
        //                     [example] => 您好，您已申请提现成功。
        //                                     提现银行：招商银行
        //                                     银行卡号：尾号0449
        //                                     开户人：郝酷
        //                                     提现金额：200元
        //                                     时间：2014年11月11日 11:11
        //                                     本次提现预计2个工作日内到达您指定银行账户，请注意查询！
        //                 )

        //         )

        // )
    }


    /**
     * redis自增测试
     */
    public function redisincr()
    {
        $ip = $this->getIP();
        if (Redis::exists($ip)) {
            Redis::incr($ip);
        } else {
            Redis::set($ip, 1);
        }
        echo Redis::get($ip);
    }


    /**
     * 验证码测试
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
        Session::put('logcode', $phrase);
        // 生成图片
        header("Cache-Control: no-cache, must-revalidate");
        header("Content-Type:image/png");
        $builder->output();
    }

    /**
     * 测试验证码-首页
     */
    public function testcaptcha()
    {
        return view('welcome');
    }

    /**
     * 测试验证码-首页
     */
    public function checkcaptcha(Request $request)
    {
        // 判断输入的验证码是否正确
        $captcha = request('captcha');
        if (Session::get('logcode') == $captcha) {
            $response = [
                'code' => '0',
                'msg'  => '验证码输入正确',
            ];
        } else {
            $response = [
                'code' => '1',
                'msg'  => '验证码输入错误，正确的验证码是：' . Session::get('logcode') . '，您输入的验证码是：' . $captcha,
            ];
        }
        // 返回结果
        return $response;
    }

    /**
     * 取出当前系统保存的验证码
     */
    public function getcaptcha()
    {
        return Session::get('logcode');
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
        // 打印的结果如下
        // Overtrue\Socialite\User Object
        // (
        //     [attributes:protected] => Array
        //         (
        //             [id] => ol0Z1uAO8pkZLapzV3SFJO-msRHg
        //             [name] => 阳鸣天下
        //             [nickname] => 阳鸣天下
        //             [avatar] => http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJNgTDyC5c3bArt4F57nVwrlVA8B2yiboib9dbtx1uFhGvmwtuHgOib763qqOjpfrL9libbqaK0zicianfQ/132
        //             [email] =>
        //             [original] => Array
        //                 (
        //                     [openid] => ol0Z1uAO8pkZLapzV3SFJO-msRHg
        //                     [nickname] => 阳鸣天下
        //                     [sex] => 1
        //                     [language] => zh_CN
        //                     [city] => 南开
        //                     [province] => 天津
        //                     [country] => 中国
        //                     [headimgurl] => http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJNgTDyC5c3bArt4F57nVwrlVA8B2yiboib9dbtx1uFhGvmwtuHgOib763qqOjpfrL9libbqaK0zicianfQ/132
        //                     [privilege] => Array
        //                         (
        //                         )

        //                 )

        //             [token] => Overtrue\Socialite\AccessToken Object
        //                 (
        //                     [attributes:protected] => Array
        //                         (
        //                             [access_token] => 14_qYvoJsMjwXCPfSvFcTKS34MjPaKRGFdCMXU9iZXwZetTaqVK82h2x_7N_JobykHeMCTlaXgKi0VQ6mOLSi6itA
        //                             [expires_in] => 7200
        //                             [refresh_token] => 14__5AEsuxiWQblhz9WM5DGSvAMF2OlGnxvAwpz8zAZMbiZy1hVgZc-VUnHLTCcMFj-Djmx_nEW3iP-o0xRPZiz2w
        //                             [openid] => ol0Z1uAO8pkZLapzV3SFJO-msRHg
        //                             [scope] => snsapi_userinfo
        //                         )

        //                 )

        //             [provider] => WeChat
        //         )

        // )
    }

    /**
     * 微信-给合伙人添加卡号
     */
    public function wxaddcard()
    {
        // 获取授权用户信息
        $user = $this->getauthuser();
        // 渲染
        return view('test.wx.cardadd', compact('user'));

    }

    /**
     * 微信-给合伙人添加卡号逻辑
     */
    public function wxaddcardstore(Request $request)
    {
        // 判断授权
        // $agent = $this->is_permit_auth();
        // 验证
        $this->validate($request, [
            'card_number' => 'required|unique:cards,card_number,' . Session::get('agent')['agent_id'],
            'name'        => 'required|string',
            'id_number'   => 'required|unique:agents,id_number,' . Session::get('agent')['agent_id'],
        ]);

        // 逻辑
        $card_number = request('card_number');
        $name = request('name');
        $id_number = request('id_number');
        $agent_id = Session::get('agent')['agent_id'];
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

            // 银行卡列表
            $cards = Card::where('agent_id', (Session::get('agent')['agent_id']))->orderBy('id', 'asc')->get();

            $branch = '未填写';
            // 用户自己添加的也为默认卡号
            $isdefault = '1';
            $newid = Card::create(compact('agent_id', 'bank_id', 'branch', 'isdefault', 'card_number'))->id;
            if (!$newid) {
                throw new \Exception('银行卡添加失败');
            }

            // agent更新
            if (!TestAgent::find($agent_id)->update(compact('name', 'id_number'))) {
                throw new \Exception('更新用户信息失败');
            }

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
     * 判断登录之后是否正确被授权
     */
    public function is_permit_auth()
    {
        // 接收数据
        $openid = $this->request->authorization;
        // 如果openid为空，那么就必须要注册&&登录
        if (empty($openid)) {
            $response = [
                'code' => '1',
                'msg'  => '非法授权，请先注册或登录',
            ];
            return $response;
        }
        // 取出数据库中查询的结果
        $agent = Agent::where('openid', $openid)->first();
        // 如果系统里没有这个用户，那么就没有权限请求这个接口
        if (!$agent) {
            $response = [
                'code' => '1',
                'msg'  => '当前合伙人不存在，请先注册或登录之后再来进行本操作',
            ];
        } else {
            $response = [
                'code' => '0',
                'data' => $agent,
            ];
        }
        return $response;
    }

    /**
     * 授权测试页面
     */
    public function authpage()
    {
        return view('test.is_permit_auth');
    }

    /**
     * log日志测试
     */
    public function log(Request $request)
    {
        return view('test.log');
    }

    /**
     * mail测试
     */
    public function sendmail()
    {


        // // 每天3点开始发送新邮件
        // $schedule->call(function() {
        //     // 判断是否有当天新备份的邮件
        //     $filepath = storage_path('app/Laravel');

        //     $aid = mt_rand(1, 9999);
        //     Wechat::create(compact('aid'));
        // })->everyMinute();


        // // 判断是否有当天新备份的邮件
        // $filepath = storage_path('app/Laravel');
        // // 获取当前文件夹中的文件列表
        // $handle = opendir($filepath);
        // // 空数组保存文件列表
        // $files_array = [];
        // while (($file = readdir($handle)) !== false) {
        //     if (($file != '.') && ($file != '..')) {
        //         $files_array[] = $file;
        //     }
        // }

        echo '<pre>';
        $files = $this->read_dir(storage_path('app/Laravel'));
        foreach ($files as $file) {
            $current_date = date('Y-m-d');
            if (strpos($file, $current_date) !== false) {
                // 如果找到了，那么就发邮件
                $message = [
                    // 邮件标题
                    'title'               => $current_date . '网站完整备份',
                    // 收件人
                    'to'                  => '806316776@qq.com',
                    // 昵称
                    'name'                => '意远支付',
                    // 邮件正文内容
                    'content'             => '尊敬的管理员，您好，附件是意远支付 hhr.yiopay.com ' . $current_date . '网站完整备份，请查收。',
                    // 附件地址
                    'attachment'          => storage_path('app/Laravel') . '/' . $file,
                    // 附件在邮件中的别名
                    'attachment_filename' => $current_date . '网站完整备份',
                ];

                // 然后发送消息队列
                // 发邮件，启用消息队列
                /**
                 * @param $message 发信参数组成的数组 array
                 * @param $template 发信模版
                 * @param $timeout 重试间隔时间，单位是秒 int
                 * @param $attempt 最大重试次数 int
                 * delay 延迟多少秒进入队列
                 */

                // 采用事务处理机制
                DB::beginTransaction();
                try {

                    // 推送到队列
                    $job = (new SendMail($message, $template = "emails.default", $timeout = 120, $attempt = 10))->delay(5);
                    if (!$this->dispatch($job)) {
                        throw new \Exception('推送 ' . $message['title'] . ' 到任务队列失败，请重试...');
                    }

                    // 提交
                    DB::commit();

                    // 记录发送日志
                    $msg = '';
                    $msg .= '<pre>' . PHP_EOL;
                    $msg .= $message['title'] . '自动备份成功，主要信息如下：' . PHP_EOL;
                    $arr = print_r($message, true);
                    $msg .= "$arr";
                    $msg .= PHP_EOL . PHP_EOL;
                    // 写入日志
                    Log::info($msg);


                } catch (\Exception $e) {
                    // 失败回滚
                    DB::rollback();
                    // 记录错误日志
                    $msg = '';
                    $msg .= '<pre>' . PHP_EOL;
                    $msg .= $message['title'] . '自动备份失败，主要信息如下：' . PHP_EOL;
                    $msg .= '备份报文：' . PHP_EOL;
                    $arr = print_r($message, true);
                    $msg .= "$arr";
                    $msg .= PHP_EOL;
                    $msg .= '错误信息：' . $e->getMessage() . PHP_EOL;
                    $msg .= PHP_EOL . PHP_EOL;
                    // 写入日志
                    Log::info($msg);
                }
            }
        }
        echo '</pre>';
    }

    /**
     * 获取文件列表，简单获取，不含递归
     */
    public function read_dir($path)
    {
        // 逻辑
        // 获取当前文件夹中的文件列表
        $handle = opendir($path);
        // 空数组保存文件列表
        $files_array = [];
        while (($file = readdir($handle)) !== false) {
            if (($file != '.') && ($file != '..')) {
                // 如果是文件夹，那么继续查找，这里使用递归
                $folder = $path . '/' . $file;
                if (filetype($folder) != 'dir') {
                    $files_array[] = $file;
                }
            }
        }
        // 返回
        return $files_array;
    }


    /**
     * 微信-登录默认首页(测试接口，完事删除)
     */
    public function wxindex(Request $request)
    {
        // 逻辑
        // 拿到配置参数
        $app_id = $this->config['app_id'];
        $secret = $this->config['secret'];
        $user = $this->agentauth->getauthuser();
        // 首页授权添加
        $method = '5';
        // 默认密码123456
        $password = bcrypt('123456');
        // 微信相关
        $wx_openid = $user['id'];
        $openid = $user['id'];
        // 判断是否有parentopenid或者invite_openid
        if (!empty($request->parentopenId)) {
            $parentopenid = $request->parentopenId;
        } else {
            // 再判断invite_openid是否存在
            if (!empty($request->invite_openid)) {
                $parentopenid = $request->invite_openid;
            } else {
                $parentopenid = null;
            }
        }

        // 再判断如果$openid和$parentopenid相等，说明自己邀请自己，那么邀请无效，$parentopenid为NULL
        if ($parentopenid == $openid) {
            $parentopenid = null;
        }

        // 保存
        // 因为要记录两次，所以这里启用事务处理
        DB::beginTransaction();
        try {

            // 把信息写入合伙人表
            $agent = Agent::where('openid', $user['id'])->first();
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
                    throw new \Exception('生成合伙人ID失败');
                }
                if (!$agent->update(compact('sid'))) {
                    throw new \Exception('更新合伙人ID失败');
                }

                // 如果agentaccount表没有这个用户，那么就新增
                if (!TestAgentAccount::firstOrCreate(['agent_id' => $agent_id], [
                    'frozen_money'    => '0.00',
                    'available_money' => '0.00',
                    'sum_money'       => '0.00',
                ])) {
                    throw new \Exception('写入用户资产表失败');
                }

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
            $arr = print_r($agent, true);
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
        return view('test.wx.wx', compact('app_id', 'secret', 'user'));
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
     * 我的团队-列表
     */
    public function wxmyteam()
    {
        // 获取授权用户信息
        $user = $this->getauthuser();
        // 渲染
        return view('test.wx.myteam', compact('user'));
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
     * 给指定的openid发送消息
     */
    public function sendwxmsg(Request $request)
    {
        // 验证
        $this->validate($request, [
            'msg'    => 'required',
            'openid' => 'required',
        ]);

        // 逻辑
        $openid = $request->openid;
        $msg = $request->msg;
        // 推送消息
        $this->app->customer_service->message($msg)->to($openid)->send();
        // 返回真
        return true;
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
        return view('test.wx.applycard', compact('bank', 'user'));
    }


    /**
     * 申请办卡-逻辑 [接口]
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
        $invite_openid = request('invite_openid');
        // 当前申请卡片模型
        $cardbox = Cardbox::find($card_id);

        // 判断逻辑
        // 采用事务处理机制
        DB::beginTransaction();
        try {
            // 首先判断系统是否存在这个合伙人，如果不存在，就添加
            // 因为手机号验证通过了，所以有效用户肯定是没有问题的
            $agent = Agent::where('mobile', $user_phone)->first();
            // 如果系统不存在这个合伙人，
            if (!$agent) {
                // 如果邀请人邀请自己注册，那么邀请无效，合伙人上级为空，邀请人为空，佣金为0，上级佣金也为0
                if ($user_openid == $invite_openid) {
                    $parentopenid = null;
                    $invite_openid = null;
                    $invite_money = '0.00';
                    $top_invite_money = '0.00';
                } else {
                    // 如果是正常点击别人的邀请链接注册，这个时候邀请人肯定是合伙人的，毋庸置疑
                    $parentopenid = $invite_openid;
                    $invite_money = $cardbox->cardAmount;
                    // 佣金、上上级佣金判断,等模型更新完之后再判断
                }
                // 添加新合伙人，也用$agent命名
                $agent = Agent::create([
                    'sname'        => $user_name,
                    'name'         => $user_name,
                    'id_number'    => $user_identity,
                    'wx_openid'    => $user_openid,
                    'openid'       => $user_openid,
                    'parentopenid' => $parentopenid,
                    'mobile'       => $user_phone,
                    // 初始密码为123456
                    'password'     => bcrypt('123456'),
                    // method为2，办卡自动添加
                    'method'       => '2',
                ]);

                // 新增失败，那么就报错
                if (!$agent->id) {
                    throw new \Exception('新增合伙人失败');
                }

                // 写入sid值
                $agent_id = $agent->id;
                $result = \DB::table('agents')->select(\DB::raw("concat('M', right(concat('00000' , id), 5)) as sid"))->where('id', $agent_id)->get();
                $sid = $result[0]->sid;

                // 如果没有sid值，那么就报错
                if (!$sid) {
                    throw new \Exception('生成合伙人SID失败');
                }

                // 取出当前注册用户的模型
                if (!$agent->update(compact('sid'))) {
                    throw new \Exception('写入合伙人SID失败');
                }

                // 写入合伙人账户余额表
                // 如果agentaccount表没有这个用户，那么就新增
                if (!AgentAccount::firstOrCreate(['agent_id' => $agent_id], [
                    'frozen_money'    => '0.00',
                    'available_money' => '0.00',
                    'sum_money'       => '0.00',
                ])) {
                    throw new \Exception('写入用户资产表失败');
                }
            } else {
                // 否则，说明系统存在这个合伙人，外面传进来的invite_id无效，推荐人openid是系统表保存的值
                $invite_openid = $agent->parentopenid;
                // 判断系统表上级合伙人是否为null
                if (empty($invite_openid)) {
                    // 如果推荐人为空，那么推荐人分润这一项为0
                    $invite_money = '0.00';
                } else {
                    // 如果不为空，那么就有推荐人就有分润
                    $invite_money = $cardbox->cardAmount;
                }

                // 那么，我们规定，如果信息不全，就自动补全信息
                // parentopenid不需要写入
                $sname = empty($agent->sname) ? $user_name : $agent->sname;
                $name = empty($agent->name) ? $user_name : $agent->name;
                $id_number = empty($agent->id_number) ? $user_identity : $agent->id_number;
                // 对于parentopenid，那么不覆盖，保持原样
                // 开始补充合伙人表
                if (!$agent->update(compact('sname', 'name', 'id_number'))) {
                    throw new \Exception('更新用户基本信息失败');
                }
            }

            // 写入订单号
            $order_id = 'CR' . date('YmdHis') . mt_rand(1000, 9999);

            // 需要判断这个推荐人还有没有上级
            $parentAgent = Agent::where('openid', $invite_openid)->first();
            // 如果没有上上级
            if (empty($parentAgent->parentopenid)) {
                $top_invite_money = '0.00';
            } else {
                // 否则就是有上上级
                $top_invite_money = $cardbox->cardTopAmount;
            }

            // 写入申请记录表
            $created_at = date('Y-m-d H:i:s');
            $applycard = ApplyCard::Create(compact('order_id', 'user_openid', 'card_id', 'invite_openid', 'invite_money', 'top_invite_money', 'user_name', 'user_identity', 'user_phone'));

            // 如果新增失败就报错
            if (!$applycard->id) {
                throw new \Exception('新增申请卡片记录失败！');
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
                            'value' => '您的客户' . $hide_user_name . '办了一张' . $card_name . '信用卡，预计返佣金额' . $invite_money . '元，返佣到账以信用卡申请通过为准，请知悉。',
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
                            "value" => "招代理最高补贴90元，赶快去招代理吧！" . PHP_EOL . '点击查看详情',
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




    // /**
    //  * 申请办卡-逻辑 [接口]
    //  */
    // public function wxapplycardstore(Request $request)
    // {
    //     // 验证
    //     $this->validate($request, [
    //         'user_openid' => 'required',
    //         'card_id' => 'required|integer',
    //         'user_name' => 'required',
    //         'user_identity' => 'required',
    //         'user_phone' => 'required',
    //     ]);
    //     // 逻辑
    //     $user_openid = request('user_openid');
    //     $card_id = request('card_id');
    //     $user_name = request('user_name');
    //     $user_identity = request('user_identity');
    //     $user_phone = request('user_phone');
    //     $invite_openid = request('invite_openid');
    //     if (!empty($invite_openid)) {
    //         $invite_money = Cardbox::find($card_id)->cardAmount;
    //     } else {
    //         $invite_money = '0.00';
    //     }

    //     // 判断逻辑
    //     $order_id = 'CR'.date('YmdHis').mt_rand(1000, 9999);
    //     $result = ApplyCard::Create(compact('order_id', 'user_openid', 'card_id', 'invite_openid', 'invite_money', 'user_name', 'user_identity', 'user_phone'));
    //     if ($result->id) {
    //         // 插入新模型
    //         $applycard = ApplyCard::find($result->id);
    //         // 申请卡片
    //         $applycard->card_name = $applycard->cardbox->merCardName;
    //         // 姓名隐藏处理
    //         $applycard->hide_user_name = $this->substr_cutname($applycard->user_name);
    //         // 手机号隐藏处理
    //         $applycard->hide_user_phone = $this->hidephone($applycard->user_phone);

    //         // 如果存在invite_openid，就推送微信，否则就不推送
    //         if (!empty($invite_openid)) {
    //             $msg = '您推荐的用户 '.$applycard->hide_user_name.'，手机号为'.$applycard->hide_user_phone.'的好友于'.$applycard->created_at.'申请了一张'.$applycard->card_name.'信用卡，预计返佣金额'.round($applycard->invite_money, 2).'元，返佣到账以信用卡申请通过为准。';
    //             $this->app->customer_service->message($msg)->to($invite_openid)->send();
    //         }

    //         // 最终返回
    //         $response = [
    //             'code' => '0',
    //             'data' => $applycard,
    //             'msg' => '信息登记成功，即将跳转到银行申请页面，请稍候...',
    //         ];
    //     } else {
    //         $response = [
    //             'code' => '1',
    //             'msg' => '信息登记失败',
    //         ];
    //     }
    //     return $response;
    // }

    /**
     * 微信-激励金明细
     */
    public function wxincentivedetail()
    {
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('test.wx.incentivedetail', compact('user'));
    }

    /**
     * 微信-提现明细列表
     */
    public function wxwithdraw()
    {
        // 渲染
        return view('test.wx.withdraw');
    }

    /**
     * 激励金记录（接口）
     * 根据openid查询，无需登录
     */
    public function wxgetincent(Request $request)
    {
        // 验证
        $this->validate($request, [
            'openid' => 'required|string',
        ]);
        // 逻辑
        $openid = $request->get('openid');
        $agent = Agent::where('openid', $openid)->first();
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
                    $finances[$k]->source = '月度分润';
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
     * 微信-账户详情
     */
    public function wxmysum()
    {
        // 获取授权用户信息
        $user = $this->getauthuser();
        // 渲染
        return view('test.wx.wxmysum', compact('user'));
    }


    /**
     * 自动把apply_cards的新纪录写入到agents表中
     */
    public function insertagents()
    {
        // 逻辑
        $apply_cards = ApplyCard::all()->toArray();
        $records = $this->second_array_unique($apply_cards, 'user_phone');

        // 开始插入agents表
        // 采用事务处理机制
        DB::beginTransaction();
        try {
            foreach ($records as $record) {
                $agent = Agent::where('mobile', $record['user_phone'])->first();

                // 如果推荐人openid和办卡openid为同一个人，那么在写入合伙人表的时候，上级openid为空，否则就是推荐人openid
                if ($record['user_openid'] == $record['invite_openid']) {
                    $parentopenid = null;
                } else {
                    $parentopenid = $record['invite_openid'];
                }

                if (!$agent) {
                    $new_agent = Agent::create([
                        'sname'        => $record['user_name'],
                        'name'         => $record['user_name'],
                        'id_number'    => $record['user_identity'],
                        'wx_openid'    => $record['user_openid'],
                        'openid'       => $record['user_openid'],
                        'parentopenid' => $parentopenid,
                        'mobile'       => $record['user_phone'],
                        // 初始密码为123456
                        'password'     => bcrypt('123456'),
                        // method为2，办卡自动添加
                        'method'       => '2',
                    ]);

                    // 新增失败，那么就报错
                    if (!$new_agent->id) {
                        throw new \Exception('新增合伙人失败');
                    }

                    // 写入sid值
                    $agent_id = $new_agent->id;
                    $result = \DB::table('agents')->select(\DB::raw("concat('M', right(concat('00000' , id), 5)) as sid"))->where('id', $agent_id)->get();
                    $sid = $result[0]->sid;

                    // 如果没有sid值，那么就报错
                    if (!$sid) {
                        throw new \Exception('生成合伙人SID失败');
                    }

                    // 取出当前注册用户的模型
                    if (!TestAgent::find($agent_id)->update(compact('sid'))) {
                        throw new \Exception('写入合伙人SID失败');
                    }

                    // 写入合伙人账户余额表
                    // 如果agentaccount表没有这个用户，那么就新增
                    if (!AgentAccount::firstOrCreate(['agent_id' => $agent_id], [
                        'frozen_money'    => '0.00',
                        'available_money' => '0.00',
                        'sum_money'       => '0.00',
                    ])) {
                        throw new \Exception('写入用户资产表失败');
                    }
                } else {
                    // 如果信息不全，那么就自动补全信息
                    $sname = empty($agent->sname) ? $record['user_name'] : $agent->sname;
                    $name = empty($agent->name) ? $record['user_name'] : $agent->name;
                    $id_number = empty($agent->id_number) ? $record['user_identity'] : $agent->id_number;
                    $parentopenid = empty($agent->parentopenid) ? $parentopenid : $agent->parentopenid;
                    // 开始写入
                    if (!$agent->update(compact('sname', 'name', 'id_number', 'parentopenid'))) {
                        throw new \Exception('更新用户基本信息失败');
                    }
                }
                // 提交
                DB::commit();
            }
        } catch (\Exception $e) {
            // 回滚
            DB::rollback();
            $response = [
                'code' => '1',
                'msg'  => $e->getMessage(),
            ];
            // 返回错误信息并记录
            return $response;
        }
    }


    /**
     * 主动发消息给用户
     * api文档：https://www.easywechat.com/docs/master/zh-CN/official-account/customer_service
     * @return bool
     */
    public function sendTo()
    {
        // 渲染
        $openid = $this->request->openid;
        return view('wechat.sendto', compact('openid'));
    }


    /**
     * 主动发消息给用户
     * api文档：https://www.easywechat.com/docs/master/zh-CN/official-account/customer_service
     * @return bool
     */
    public function sendToPost()
    {
        // 逻辑
        $openid = $this->request->openid;
        $message = $this->request->message;
        $result = $this->app->customer_service->message($message)->to($openid)->send();
        return $result;
    }

    /**
     * 测试微信参数
     */
    public function testwx()
    {
        // 如果存在invite_openid，就推送微信模板，否则就不推送
        $invite_openid = 'ol0Z1uAO8pkZLapzV3SFJO-msRHg';
        $hide_user_name = '刘洪伸';
        $card_name = '兴业银行';
        $invite_money = '45';
        $littleFlag = '大额';
        $created_at = '2008-08-08 08:08:08';
        if (!empty($invite_openid)) {
            // 开始推送推荐成交通知模板
            $this->app->template_message->send([
                'touser'      => $invite_openid,
                'template_id' => 'PcGOMAmyFCBqklWpWSVX_0w-70JMwObKHu9TfMDO8JM',
                // 这里推送当前用户的推广链接
                'url'         => 'http://hhr.yiopay.com/agent/wx?wxshare=wxshare&appuuid=wx88d48c474331a7f5&parentopenId=' . $invite_openid,
                'data'        => [
                    'first'    => [
                        'value' => '您的客户' . $hide_user_name . '办了一张' . $card_name . '信用卡，预计返佣金额' . $invite_money . '元，返佣到账以信用卡申请通过为准，请知悉。',
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
                        "value" => $card_name . '【' . $littleFlag . '】',
                        "color" => "#173177",
                    ],
                    "remark"   => [
                        "value" => "招代理最高补贴90元，赶快去招代理吧！" . PHP_EOL . '点击查看详情',
                        "color" => "#173177",
                    ],
                ],
            ]);
        }

        return [
            'code' => '0',
            'msg'  => '发送成功',
        ];

        // 逻辑
        $token = $this->request->token;
        $timestamp = $this->request->timestamp;
        $nonce = $this->request->nonce;
        $tmpArr = [$token, $timestamp, $nonce];
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        // 返回
        return $tmpStr;

        // 测试网址 http://hhr.yiopay.com/test/testwx?signature=600842ee68ed8bea9f2c0ac138a8f0c803d00a14&echostr=8085854468487076604&timestamp=1530682104&nonce=1130401568&token=easywechat
        // 最后返回 600842ee68ed8bea9f2c0ac138a8f0c803d00a14
    }

    /**
     * 文件自动添加版本号
     * @param $file 文件绝对路径
     * 调用规则：<link rel="stylesheet" href="<?=AutoVersion('assets/css/style.css')?>" type="text/css" />
     * 如果存在，那么就是如下的形式：
     * <link rel="stylesheet" href="assets/css/style.css?v=1367936144322" type="text/css" />
     */
    public function AutoVersion($file)
    {
        if (file_exists($file)) {
            $ver = filemtime($file);
        } else {
            $ver = 1;
        }
        return $file . '?v=' . $ver;
    }

    /**
     * 调用自动版本号
     */
    public function testautoversion()
    {
        // 逻辑
        $format_file = $this->AutoVersion(public_path('static/js/common.js'));
        echo $format_file;
    }

    /**
     * 微信-我的默认首页
     */
    public function wxmine()
    {
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('test.wx.wxmine', compact('user'));
    }

    /**
     * 微信-进度查询
     */
    public function wxprogress()
    {
        // 渲染
        return view('test.wx.progress');
    }

    /**
     * 微信-我的订单
     */
    public function wxorder()
    {
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('test.wx.order', compact('user'));
    }

    /**
     * 微信-我的信息
     */
    public function wxmessage()
    {
        // 渲染
        // return view('agent.message');
        // 取出授权user
        $user = $this->getauthuser();
        // 渲染
        return view('test.wx.message', compact('user'));
    }

    /**
     * 微信-卡号列表(模板用)
     */
    public function wxrankcard()
    {
        // 取出授权user，Changing
        $user = $this->getauthuser();
        // 渲染
        return view('test.wx.rankcard', compact('user'));

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
        // 渲染
        return view('test.wx.invitation', compact('app_id', 'secret', 'user'));
    }

    /**
     * 微信-我的客服
     */
    public function wxcustomerService()
    {
        // 渲染
        return view('test.wx.customservice');
    }

    /**
     * 微信-设置
     */
    public function wxsetting()
    {
        // 逻辑
        // 获取授权用户信息
        $user = $this->getauthuser();
        // 渲染
        return view('test.wx.setting', compact('user'));
    }

    /**
     * 微信-登录页面
     */
    public function wxlogin()
    {
        // 渲染
        return view('test.wx.wxlogin');
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
        return view('test.wx.cardinfo', compact('bank', 'user', 'bankid'));
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
        return view('test.wx.identityforreal', compact('user'));
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
            $agent = Agent::where('openid', $openid)->first();
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
                if (!TestAgentAccount::firstOrCreate(['agent_id' => $agent_id], [
                    'frozen_money'    => '0.00',
                    'available_money' => '0.00',
                    'sum_money'       => '0.00',
                ])) {
                    throw new \Exception('写入用户资产表失败');
                }

            }

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
        return view('test.wx.agreement');
    }

    /**
     * 检查当前授权的openid用户是否已经注册，如果注册，那么取出这个合伙人模型
     */
    public function wxcheckbyopenid()
    {
        // 逻辑
        $openid = $this->request->openid;
        $agent = Agent::where('openid', $openid)->first();
        // 取出合伙人模型
        if ($agent) {
            $response = [
                'code' => '0',
                'data' => $agent,
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
     * 判断当前用户是否进行了实名认证
     */
    public function wxisreal()
    {
        // 逻辑
        $openid = $this->request->openid;
        // 取出用户合伙人模型
        $agent = Agent::where('openid', $openid)->first();
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
        $agent = Agent::where('openid', $openid)->first();
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
     * 发送4位数字验证码
     */
    public function createcode()
    {
        // 生成的验证码保存在cache里，默认5分钟有效期，测试用3000分钟
        Cache::put('testwxyzm', mt_rand(1000, 9999), 3000);
        return Cache::get('testwxyzm');
    }

    /**
     * 临时获取4位数字验证码
     */
    public function getwxcode()
    {
        // 逻辑
        return Cache::get('testwxyzm');
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
        if (Cache::get('testwxyzm') == $capcha) {
            $data = [
                'code' => '0',
                'msg'  => '验证码输入正确',
            ];
        } else {
            $data = [
                'code' => '1',
                'yzm'  => Cache::get('testwxyzm'),
                'msg'  => '验证码输入错误',
            ];
        }
        return $data;
    }

    /**
     * 银行四要素认证接口
     * 测试，不需要接口，注释即可
     */
    public function checkbankcard(Request $request)
    {
        // 接收数据
        // 如果系统里没有这个用户，那么就没有权限请求这个接口
        $openid = $this->request->authorization;
        // 使用Test表
        $agent = Agent::where('openid', $openid)->first();
        if (!$agent) {
            $response = [
                'code' => '1',
                'msg'  => '当前合伙人不存在，绑卡失败，请先注册合伙人再进行此操作',
            ];
            return $response;
        }
        $name = $request->get('name');
        $idcardno = $request->get('idcardno');
        $bankcardno = $request->get('bankcardno');
        $tel = $request->get('tel');

        // 这里强制返回成功
        $response = [
            'isok' => '1',
            'code' => '1',
            'desc' => '持卡人认证成功',
            'data' => [
                'bankcardno' => $bankcardno,
                'name'       => $name,
                'idcardno'   => $idcardno,
                'tel'        => $tel,
            ],
        ];
        // 返回
        return $response;

        // $url = 'http://api.id98.cn/api/v2/bankcard?appkey='.self::MSG_APPKEY.'&name='.$name.'&idcardno='.$idcardno.'&bankcardno='.$bankcardno.'&tel='.$tel;

        // 记录绑卡的url地址
        $msg = PHP_EOL . '绑卡报文如下：' . PHP_EOL;
        $msg .= '当前绑卡的用户openid：' . $openid . PHP_EOL;
        $msg .= '用户模型如下：' . PHP_EOL . PHP_EOL;
        $msg .= '<pre>' . PHP_EOL;
        $arr = print_r($agent, true);
        $msg .= "$arr";
        // $msg .= '请求url地址：'.$url.PHP_EOL;
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

}
