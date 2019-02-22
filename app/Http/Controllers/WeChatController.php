<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use EasyWeChat\Kernel\Messages\Transfer;
// 微信存储消息
use App\Model\WechatMessage;

class WeChatController extends Controller
{
    // 初始化公众号
    protected $app;
    // 请求
    protected $request;

    // 构造函数
    // 使用request注入，便于session整合
    public function __construct(Request $request)
    {
        // request注入
        $this->request = $request;

        // 全局配置
        $config = Config::get("wechat.official_account.default");

        // 使用配置来初始化一个公众号应用实例
        $this->app = Factory::officialAccount($config);
    }

    /**
     * 处理微信的请求消息
     * @return string
     */
    public function serve()
    {
        // 推送消息
        $this->app->server->push(function ($message) {
            // 到这里，说明用户已经进入到微信号了，那么就继续
            // 拿到openid
            $openId = $message['FromUserName'];
            // 取得用户详细信息，如果redis里面有，那么就不要从微信服务器上拿
            if (Session::has('wechat.oauth_user')) {
                $user = Session::get('wechat.oauth_user');
            } else {
                // 取出用户信息
                $user = $this->app->user->get($openId);
                $this->request->session()->put('wechat.oauth_user', json_encode($user));
            }

            // 拿到数组
            $user_arr = json_decode(Session::get('wechat.oauth_user'), true);

            // 根据传过来的类型判断消息类型
            switch ($message['MsgType']) {
                // 如果是事件类型
                case 'event':
                    // 判断是关注还是取消关注，如果是关注
                    if ($message['Event'] == 'subscribe') {
                        // 接入Log日志
                        // 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志
                        $msg = '';
                        $msg .= '<pre>' . PHP_EOL;
                        $msg .= '用户' . $openId . '已经关注了意远合伙人公众号，该用户的详细资料如下：' . PHP_EOL;
                        $arr = print_r($user_arr, true);
                        $msg .= "$arr";
                        $msg .= PHP_EOL . PHP_EOL;
                        // 写入日志
                        Log::info($msg);

                        // 返回
                        return '尊敬的' . $user['nickname'] . '，您好，感谢关注意远合伙人。' . PHP_EOL . PHP_EOL . '在意远办卡完全免费，不需要交一分钱！分享办卡最高可得90元/张返佣，0投资，赶快行动吧！';

                    } else if ($message['Event'] == 'VIEW') {

                        // 如果是view则写入
                        $msg = '';
                        $msg .= '当前用户的操作行为是：' . $message['Event'] . "，也就是说点击了下面的合伙人按钮。\n";
                        // 写入日志
                        Log::info($msg);

                    } else if ($message['Event'] == 'unsubscribe') {
                        // 如果是取消关注
                        // 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志
                        $msg = '';
                        $msg .= '<pre>' . PHP_EOL;
                        $msg .= '当前用户的操作行为是：' . $message['Event'] . PHP_EOL;
                        $msg .= '用户' . $openId . '已经取消关注了意远合伙人公众号，该用户的详细资料如下：' . PHP_EOL;
                        $arr = print_r($user_arr, true);
                        $msg .= "$arr";
                        $msg .= PHP_EOL . PHP_EOL;
                        // 写入日志
                        Log::info($msg);

                        // 取消关注就从redis中删除
                        if (Session::has('wechat.oauth_user')) {
                            // 删除微信登录缓存
                            Session::forget('wechat.oauth_user');
                        }

                    } else {
                        // 如果是其他行为，则另行判断，api里面没写
                        $msg = '';
                        $msg .= '当前用户的操作行为是：' . $message['Event'] . PHP_EOL;
                        // 写入日志
                        Log::info($msg);
                    }
                    break;

                // 如果是文本消息类型
                case 'text':

                    // 接收的内容写入Log日志
                    // 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志
                    $msg = '';
                    $msg .= '<pre>' . PHP_EOL;
                    $msg .= '用户' . $openId . '向您发送了一条消息，该用户的详细资料如下：' . PHP_EOL;
                    $arr = print_r($user_arr, true);
                    $msg .= "$arr";
                    $msg .= PHP_EOL . PHP_EOL;
                    $msg .= "发送的消息内容为：" . $message['Content'];
                    $msg .= PHP_EOL . PHP_EOL;
                    // 写入日志
                    Log::info($msg);

                    // 接收数字
                    // 如果是openid
                    if ($message['Content'] == 'openid') {
                        return $user_arr['openid'];
                    } else if ($message['Content'] == 'wxuser') {
                        // 如果是wxuser
                        return Session::get('wechat.oauth_user');
                    } elseif (strpos($message['Content'], 'HF｜') !== false || strpos($message['Content'], 'hf｜') !== false) {
                        // 如果是回复内容，那么就依次取出
                        // 取出openid
                        $arr = explode('｜', $message['Content']);
                        if (count($arr) < 3) {
                            return '您回复的信息有误，请重新输入！';
                        }
                        // 发送消息
                        $this->app->customer_service->message($arr[2])->to($arr[1])->send();
                        return '您的消息已成功送达到 ' . $user_arr['nickname'] . '，请耐心等待对方回复~';
                    } else {
                        // 如果非测试字符串，那么就录入数据库
                        // 用户openid
                        $ask_openid = $openId;
                        $ask_user = addslashes(Session::get('wechat.oauth_user'));
                        $ask_msg = $message['Content'];
                        // 留言时间
                        $created_at = date('Y-m-d H:i:s', $message['CreateTime']);
                        $updated_at = $created_at;
                        $wechat_message = WechatMessage::create(compact('ask_openid', 'ask_user', 'ask_msg', 'created_at', 'updated_at'));
                        if (!$wechat_message) {
                            return '写入微信消息失败！';
                        } else {
                            $new_msg_id = $wechat_message->id;
                        }

                        // 然后发送给管理员
                        // 李龙的openid
                        $admin_openid = 'ol0Z1uLbitksYmYY9IDKfuVsiU1g';
                        // 超级用户的openid
                        // $superuser_openid = 'ol0Z1uAO8pkZLapzV3SFJO-msRHg';
                        // 安总的openid
                        $an_openid = 'ol0Z1uKKDG7lHEAzwMvf0W21FCgw';
                        $msg = '';
                        $msg .= '您好，"意远合伙人"公众号收到一条新留言，请知悉~' . PHP_EOL . PHP_EOL;
                        // $msg .= '该留言的相关信息如下：'.PHP_EOL;
                        $msg .= '用户昵称：' . $user_arr['nickname'] . PHP_EOL;
                        // $msg .= 'openid：'.$user_arr['openid'].PHP_EOL;
                        $msg .= '留言内容：' . $message['Content'] . PHP_EOL;
                        $msg .= '留言时间：' . $created_at . PHP_EOL . PHP_EOL;
                        // $msg .= '如果需要回复该留言，请输入如下指令：'.PHP_EOL.PHP_EOL;
                        $msg .= '如果需要回复该留言，请点击下面的链接地址：' . PHP_EOL . PHP_EOL;
                        // $msg .= 'HF｜'.$openId.'｜这里写消息内容，注意第2个|前面的内容千万不要删除';
                        // 改成链接形式的提交
                        $msg .= '<a href="http://hhr.yiopay.com/wechat/' . $new_msg_id . '">我要回复</a>';
                        // 给李龙发
                        $this->app->customer_service->message($msg)->to($admin_openid)->send();
                        // 超级用户【刘宗阳】
                        // $this->app->customer_service->message($msg)->to($superuser_openid)->send();
                        // 给安总发
                        $this->app->customer_service->message($msg)->to($an_openid)->send();
                    }

                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                case 'file':
                    return '收到文件消息';
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }
        });

        // 执行服务端
        $response = $this->app->server->serve();

        // 返回结果
        return $response;
    }

    /**
     * 获取所有模板列表
     * @return array
     */
    public function getTemplates()
    {
        return $this->app->template_message->getPrivateTemplates();
    }

    /**
     * 获取当前的自定义菜单
     * @return array
     */
    public function getMenus()
    {
        return $this->app->menu->current();
    }

    /**
     * 创建临时二维码
     * @return array
     */
    public function createQrcode()
    {
        // 逻辑
        $result = $this->app->qrcode->temporary('foo', 6 * 24 * 3600);
        return $result;

        // Array
        // (
        //     [ticket] => gQFD8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyTmFjVTRWU3ViUE8xR1N4ajFwMWsAAgS2uItZAwQA6QcA
        //     [expire_seconds] => 518400
        //     [url] => http://weixin.qq.com/q/02NacU4VSubPO1GSxj1p1k
        // )

    }

    /**
     * 获取二维码网址
     * @param $ticket createQrcode()方法中生成的ticket
     * @return string
     */
    public function getQrcodeUrl()
    {
        // 逻辑
        // 获取参数
        $ticket = $this->request->ticket;
        $url = $this->app->qrcode->url($ticket);
        return $url;

        // https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQEh8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyUnU5Wm95bjY5UlUxRnRfUzFyY3MAAgRd1q5bAwQA6QcA
    }

    /**
     * 获得二维码内容
     * @param $url 二维码图片地址
     * @return bool
     */
    public function getQrcodeContent()
    {
        // 逻辑
        $url = $this->getQrcodeUrl($this->request->ticket);
        // 得到二进制图片内容
        $content = file_get_contents($url);
        // 写入文件
        file_put_contents(public_path() . '/static/images/code.png', $content);
    }

    /**
     * 获得用户列表
     * @return array
     */
    public function getUsers()
    {
        // 逻辑
        $users = $this->app->user->list();
        return $users;

        // // result
        // {
        //     "total": 2,
        //     "count": 2,
        //     "data": {
        //       "openid": [
        //         "OPENID1",
        //         "OPENID2"
        //       ]
        //     },
        //     "next_openid": "NEXT_OPENID"
        //   }
    }

    /**
     * 获取单个用户的信息
     * @return array
     */
    public function getUser()
    {
        // 逻辑
        $openid = $this->request->openid;
        // 数组
        $user = $this->app->user->get($openid);
        // 返回
        return $user;
    }

    /**
     * 获取用户标签列表
     * @return array
     */
    public function getUserTags()
    {
        // 逻辑
        $tags = $this->app->user_tag->list();
        return $tags;
    }

    /**
     * 长链接转短链接
     * @return array
     */
    public function getShorten()
    {
        // 逻辑
        $shortUrl = $this->app->url->shorten('https://easywechat.com');
        return $shortUrl;

        // {
        //     errcode: 0,
        //     errmsg: "ok",
        //     short_url: "https://w.url.cn/s/A0KPijN"
        // }
    }

    /**
     * 获取所有客服
     * api文档：https://www.easywechat.com/docs/master/zh-CN/official-account/customer_service
     * @return array
     */
    public function getServices()
    {
        // 逻辑
        $services = $this->app->customer_service->list();
        // 返回
        return $services;

        // {
        //     kf_list: [
        //         {
        //             kf_account: "kf2001@yiopay",
        //             kf_headimgurl: "http://mmbiz.qpic.cn/mmbiz_jpg/yInlAefHIyVSbc7oib4RNcuXt9poUTCeQOBdD0zAiaq7E1bbph3xL7f67x1PtOkN9c7ntBQU4RNNICLibgp6ZZicTQ/300?wx_fmt=jpeg",
        //             kf_id: 2001,
        //             kf_nick: "小龙",
        //             kf_wx: "lilong5789"
        //         }
        //     ]
        // }        
    }

    /**
     * 获取所有在线的客服
     * api文档：https://www.easywechat.com/docs/master/zh-CN/official-account/customer_service
     * @return array
     */
    public function getOnlineServices()
    {
        // 逻辑
        $services = $this->app->customer_service->online();
        return $services;

        // {
        //     kf_online_list: [
        //         {
        //             kf_account: "kf2003@yiopay",
        //             status: 1,
        //             kf_id: 2003,
        //             accepted_case: 0
        //         }
        //     ]
        // }
    }

    /**
     * 主动发消息给用户
     * api文档：https://www.easywechat.com/docs/master/zh-CN/official-account/customer_service
     * @return bool
     */
    public function sendTo()
    {
        // 逻辑
        $openid = 'ol0Z1uAO8pkZLapzV3SFJO-msRHg';
        $message = '有人给公众号发了一条消息';
        $result = $this->app->customer_service->message($message)->to($openid)->send();
        return $result;
    }

}
