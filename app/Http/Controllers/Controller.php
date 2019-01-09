<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Session;

class Controller extends BaseController
{
    // 短信key
    const MSG_APPKEY = '276f2e741f4ec70285a4de40ad378247';

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 获取当前控制器和方法
     * @return array
     */
    public function getControllerAction()
    {
        if (\Route::current() !== NULL) {
            $action = \Route::current()->getActionName();
            list($class, $method) = explode('@', $action);
            $class = substr(strrchr($class,'\\'),1);
            return ['controller' => $class, 'action' => $method];
        }
    }

    /**
     * 含有中文字符的序列化，反序列化解决方案
     */
    public function mb_unserialize($str) {
        return preg_replace_callback('#s:(\d+):"(.*?)";#s',function($match){return 's:'.strlen($match[2]).':"'.$match[2].'";';},$str);
    }

    /**
     * 判断是否为微信内部浏览器访问
     */
    public function is_weixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    /**
     * curl模拟http发送get或post接口测试
     * $url 请求的url
     * $type 请求类型
     * $res 返回数据类型
     * $arr post请求参数
     */
    public function http_curl($url, $type='get', $res='json', $arr='')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if ($type == 'post') {
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $arr);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        if($res == 'json') {
            return json_decode($output, true);
        }
    }

    /**
     * PHP 获取客户端ip地址
     */
    public function getIP()
    {
        //strcasecmp 比较两个字符，不区分大小写。返回0，>0，<0。
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $res =  preg_match ('/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
        // 返回真实ip地址
        return $res;
    }

    /**
     * 手机号隐藏后四位
     */
    public function hidephone($phone)
    {
        return substr_replace($phone, '****', 3, 4);
    }

    /**
     * 只保留字符串首尾字符，隐藏中间用*代替（两个字符时只显示第一个）
     * @param string $user_name 姓名
     * @return string 格式化后的姓名
     */
    public function substr_cutname($user_name)
    {
        $strlen = mb_strlen($user_name, 'utf-8');
        $firstStr = mb_substr($user_name, 0, 1, 'utf-8');
        $lastStr = mb_substr($user_name, -1, 1, 'utf-8');
        return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
    }

    /**
     * 多维数组按照某一个字段去重
     * @param $arr 传入数组
     * @param $key 判断的key值
     * @return array 返回一个去重的数组
     */
    public function second_array_unique($arr, $key) {
        //建立一个目标数组  
        $res = array();        
        foreach ($arr as $value) {           
            //查看有没有重复项  
            if(isset($res[$value[$key]])) {  
                unset($value[$key]);  //有：销毁
            } else {    
                $res[$value[$key]] = $value;  
            }
        }  
        return $res;
    }

    /**
     * 生成0-1的随机数
     * @return float
     */
    public function randomFloat($min = 0, $max = 1) {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }

    /**
     * 文件自动添加版本号
     * @param $file 文件绝对路径
     * 调用规则：<link rel="stylesheet" href="<?=AutoVersion('/assets/css/style.css')?>" type="text/css" /> 
     * 如果存在，那么就是如下的形式：
     * <link rel="stylesheet" href="/assets/css/style.css?v=1367936144322" type="text/css" />
     */
    public function AutoVersion($file)
    {
        if(file_exists($_SERVER['DOCUMENT_ROOT'].$file)) {
            $ver = filemtime($_SERVER['DOCUMENT_ROOT'].$file);
        } else {
            $ver = 1;
        }
        return $file.'?v=' .$ver;
    }

}
