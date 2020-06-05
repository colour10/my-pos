<?php

// 引入
use \Illuminate\Support\Facades\Cache;
use \Illuminate\Support\Facades\Log;

// 微信jssdk类
class JSSDK {

    // 属性
    private $appId;
    private $appSecret;
    // 和用户openid进行绑定
    private $openid;

    /**
     * 构造函数
     */
    public function __construct($appId, $appSecret, $openid) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->openid = $openid;
    }

    /**
     * 获取签名令牌
     */
    public function getSignPackage() {
        // 先拿到jsapi_ticket
        $jsapiTicket = $this->getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
        "appId"     => $this->appId,
        "nonceStr"  => $nonceStr,
        "timestamp" => $timestamp,
        "url"       => $url,
        "signature" => $signature,
        "rawString" => $string
        );
        // 返回
        return $signPackage; 
    }

    /**
     * 创建安全字符串
     */
    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 获取jsapiticket
     */
    private function getJsApiTicket() {
        // jsapi_ticket 应该全局存储与更新，已经改为Redis存储
        // 首先判断有没有缓存记录
        if (Cache::has('jsapi_ticket_'.$this->openid.'_cache')) {
            // 如果存在缓存，那么判断有没有过期
            $data = json_decode(Cache::get('jsapi_ticket_'.$this->openid.'_cache'));
            if ($data->expire_time < time()) {
                $ticket = $this->createJsApiTicket($data);
            } else {
                $ticket = $data->jsapi_ticket;
            }
        } else {
            // 如果没有缓存记录
            // 那么就重新记录并写入
            $data = new stdClass();
            $ticket = $this->createJsApiTicket($data);
        }
        // 返回
        return $ticket;
    }

    /**
     * 写入JsApiTicket
     */
    private function createJsApiTicket($data) {
        // 重新写入
        $accessToken = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
        $res = json_decode($this->httpGet($url));
        $ticket = $res->ticket;
        if ($ticket) {
            $data->expire_time = time() + 7000;
            $data->jsapi_ticket = $ticket;
            // 把缓存有效期设置的比expire_time要长一点
            Cache::put('jsapi_ticket_'.$this->openid.'_cache', json_encode($data), now()->addSeconds(7100));
        }
        // 记录日志
        Log::info('正在把jsapi_ticket:'.$ticket.'写入缓存');
        // 返回
        return $ticket;
    }

    /**
     * 获取access_token
     */
    private function getAccessToken() {
        // access_token 应该全局存储与更新，已经改为Redis存储
        // 首先判断有没有缓存记录
        if (Cache::has('access_token_'.$this->openid.'_cache')) {
            // 如果存在缓存，那么判断有没有过期
            $data = json_decode(Cache::get('access_token_'.$this->openid.'_cache'));
            if ($data->expire_time < time()) {
                $access_token = $this->createAccessToken($data);
            } else {
                $access_token = $data->access_token;
            }
        } else {
            $data = new stdClass();
            $access_token = $this->createAccessToken($data);
        }
        // 返回
        return $access_token;
    }

    /**
     * 创建access_token
     * @param $data {access_token对象}
     * @return {string}
     */
    private function createAccessToken($data) {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
        $res = json_decode($this->httpGet($url));
        $access_token = $res->access_token;
        if ($access_token) {
            $data->expire_time = time() + 7000;
            $data->access_token = $access_token;
            // 把缓存有效期设置的比expire_time要长一点
            Cache::put('access_token_'.$this->openid.'_cache', json_encode($data), now()->addSeconds(7100));
        }
        // 记录日志
        Log::info('正在把access_token:'.$access_token.'写入缓存');
        // 返回
        return $access_token;
    }

    /**
     * 请求
     */
    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }
}