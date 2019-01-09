<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
// 为了防止死循环，这里调用DB类而不是ORM进行操作
use Illuminate\Support\Facades\DB;
// redis私人订制
// use Symfony\Component\Cache\Simple\RedisCache;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Config;
// 记录日志
use Illuminate\Support\Facades\Log;

class MakePayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    // 属性
    // 合伙人模型
    protected $agent;
    // 合伙人钱包表
    protected $agentaccount;
    // 支付通道
    protected $method;
    // 卡信息
    protected $card;
    // 提现数据表记录模型
    protected $withdraw;
    // 设置重试时间
    protected $timeout;
    // 设置最大重试次数
    protected $attempt;
    // 查询结果
    protected $result;
    // 微信openid
    protected $openid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($agent, $agentaccount, $card, $method, $withdraw, $timeout=300, $attempt=10)
    {
        // agent表
        $this->agent = $agent;

        // agentaccount表
        $this->agentaccount = $agentaccount;

        // 卡表
        $this->card = $card;

        // 支付通道表
        $this->method = $method;

        // 当前提现数据库记录
        $this->withdraw = $withdraw;

        // 重试时间
        $this->timeout = $timeout;

        // 最大重试次数
        $this->attempt = $attempt;

        // 查询结果
        $this->result = '';

        // 微信openid，这个是没有被污染的原始的openid值，防止wx_openid被恶意覆盖
        $this->openid = $this->agent->openid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 如果第一次接收，那么就选择09400业务代码
        if ($this->attempts() == 1) {

            // 第一次状态查询提示
            $msg = "\n".'用户openid：'.$this->openid.'正在对 '.$this->withdraw->cash_id.' 进行第1次状态查询，请稍候...'."\n\n";
            Log::info($msg);
            echo $msg;

            // 赋值【获取第一次报文发送结果】
            $this->result = $this->getFirstCode();

            // 之后的result值，把print_r的结果保存在一个变量当中
            $msg = '';
            $msg .= '<pre>'."\n";
            $msg .= $this->withdraw->cash_id.' 第1次状态查询的结果如下：'."\n";
            $arr = print_r($this->result, true);
            $msg .= "$arr";
            $msg .= "\n\n";
            // 写入日志
            Log::info($msg);
            echo $msg;

            // 判断交易状态
            $this->checkStatus();

        } elseif ($this->attempts() > $this->attempt) {

            // 如果超过最大次数，那么就确认为失败，并且删除队列中的任务
            $this->delete();
            
        } else {

            // 第一次之后的重试
            $msg = "\n".'正在对 '.$this->withdraw->cash_id.' 进行第'.$this->attempts().'次状态查询，请稍候...'."\n\n";
            Log::info($msg);
            echo $msg;

            // 否则就进行二次及以后的查询
            // 赋值
            $this->result = $this->getSecondCode();

            // 之后的result值，把print_r的结果保存在一个变量当中
            $msg = '';
            $msg .= '<pre>'."\n";
            $msg .= $this->withdraw->cash_id.' 第'.$this->attempts().'次状态查询的结果如下：'."\n";
            $arr = print_r($this->result, true);
            $msg .= "$arr";
            $msg .= "\n\n";
            // 写入日志
            Log::info($msg);
            echo $msg;

            // 判断交易状态
            $this->checkStatus();

        }

    }

    /**
     * 通联支付接口-业务逻辑实例化
     */
    public function getInterface()
    {
        // 引入通联接口
        // 引入三个类文件
        $base_url = base_path('public');
        // 如果是通联线上环境，那么就读取PhpTools.class.php，负责就读取PhpToolsTest.class.php
        if ($this->method->id == 1) {
            require_once $base_url . "/backend/allinpayInter/libs/PhpTools.class.php";
        } elseif ($this->method->id == 2) {
            require_once $base_url . "/backend/allinpayInter/libs/PhpToolsTest.class.php";
        }
        require_once $base_url . "/backend/allinpayInter/libs/ArrayXml.class.php";
        require_once $base_url . "/backend/allinpayInter/libs/cURL.class.php";
        return \PhpTools::getInstance();
    }

    /**
     * 取出第一次发送的报文
     */
    public function getParams()
    {
        // 参数
        $username = $this->method->username;
        $password = $this->method->password;
        $cash_id = $this->withdraw->cash_id;
        $business_code = $this->method->business_code;
        $merchant_id = $this->method->merchant_id;
        $agent_sid = $this->agent->agent_sid;
        $card_number = $this->card->card_number;
        $agent_name = $this->agent->name;
        $account = $this->withdraw->account;
        $remark = $this->withdraw->remark;

        // 源数组(合伙人提现到自己的银行卡，金额是$account)
        $params = array(
            'INFO' => array(
                // 交易代码，必填
                'TRX_CODE' => '100014',
                // 版本，必填
                'VERSION' => '03',
                // 数据格式，必填，2代表XML
                'DATA_TYPE' => '2',
                // 处理级别，必填，0-9 0优先级最低
                'LEVEL' => '6',
                // 用户名，必填
                // 'USER_NAME' => '20060400000044502', // 测试账户用户名
                'USER_NAME' => "$username", // 正式账户用户名
                // 用户密码，必填
                // 'USER_PASS' => '`12qwe',    // 测试账户密码
                'USER_PASS' => "$password",    // 正式账户密码
                // 交易流水号，必填，建议格式：商户号+时间+固定位数顺序流水号
                'REQ_SN' => "$cash_id",
            ),
            'TRANS' => array(
                // 业务代码，必填
                // 'BUSINESS_CODE' => '09400',
                'BUSINESS_CODE' => "$business_code", // 正式环境
                // 商户代码，必填
                // 'MERCHANT_ID' => '200604000000445', // 测试商户代码
                'MERCHANT_ID' => "$merchant_id", // 正式商户代码
                // 提交时间，必填，YYYYMMDDHHMMSS
                'SUBMIT_TIME' => date('YmdHis', time()),
                // 用户编号，非必填，开发人员可当作备注字段
                'E_USER_CODE' => "$agent_sid",
                // 银行代码，必填，农业银行是0103
                'BANK_CODE' => '',
                // 账号类型，非必填，00银行卡，01存折，02信用卡。不填默认为银行卡00
                'ACCOUNT_TYPE' => '00',
                // 银行卡或存折号码，必填
                'ACCOUNT_NO' => "$card_number",
                // 银行卡或存折上的所有人姓名，必填
                'ACCOUNT_NAME' => "$agent_name",
                // 账号属性，必填，0私人，1公司。不填时，默认为私人0
                'ACCOUNT_PROP' => '0',
                // 金额，单位：分，必填
                'AMOUNT' => $account * 100,
                // 货币类型，非必填，人民币：CNY, 港元：HKD，美元：USD。不填时，默认为人民币
                'CURRENCY' => 'CNY',
                // 开户证件类型，非必填，0：身份证,1: 户口簿，2：护照,3.军官证,4.士兵证，5. 港澳居民来往内地通行证,6. 台湾同胞来往内地通行证,7. 临时身份证,8. 外国人居留证,9. 警官证, X.其他证件
                'ID_TYPE' => '0',
                // 自定义用户号，非必填，商户自定义的用户号，开发人员可当作备注字段使用
                'CUST_USERID' => "$agent_sid",
                // 交易附言，非必填，填入网银的交易备注
                'SUMMARY' => '提现',
                // 备注，非必填，供商户填入参考信息
                'REMARK' => "$remark",
            ),
        );

        // 记录报文结果
        // 为了可控，需要获取报文的详细参数
        $msg = '';
        $msg .= '<pre>'."\n";
        $msg .= '第1次发送的报文编号：'.$cash_id."\n";
        $msg .= '第1次发送的报文参数：'."\n";
        $arr = print_r($params, true);
        $msg .= "$arr";
        $msg .= "\n\n";
        // 将报文内容写入日志
        Log::info($msg);
        // 在终端显示
        echo $msg;

        // 最终返回报文结果
        return $params;
    }

    /**
     * 第一次获取的结果
     * 业务代码：100014
     */
    public function getFirstCode()
    {
        // 获得实例化模型
        $tools = $this->getInterface();

        // 取出报文内容
        $params = $this->getParams();

        // 发起请求
        $result = $tools->send($params);

        // 判断结果
        if($result == FALSE) {
            // 这个是证书错误，必须提现失败，并删除
            // 发起提现订单号
            $cash_id = $this->withdraw->cash_id;
            $format_result = [
                'AIPG' => [
                    'INFO' => [
                        'TRX_CODE' => '100014',
                        'VERSION' => '03',
                        'DATA_TYPE' => '2',
                        'REQ_SN' => "$cash_id",
                        'RET_CODE' => '3999',
                        'ERR_MSG' => '验签失败，请检查通联公钥证书是否正确',
                    ],
                ],
            ];
            // 按照数据返回格式进行拼凑
            $this->result = $format_result;
            // 最终失败
            $this->fail();
        } else {
            // 返回结果
            return $result;
        }
    }


    /**
     * 重新请求接口
     * 业务代码：200004
     */
    public function getSecondCode()
    {

        // 获得实例化模型
        $tools = $this->getInterface();

        // 参数
        $username = $this->method->username;
        $password = $this->method->password;
        $cash_id = $this->withdraw->cash_id;
        $merchant_id = $this->method->merchant_id;

        // 接下来进入下一步，判断Transret状态码
        // 源数组
        $params = array(
            'INFO' => array(
                'TRX_CODE' => '200004',
                'VERSION' => '03',
                'DATA_TYPE' => '2',
                'LEVEL' => '6',
                'USER_NAME' => $username,
                'USER_PASS' => $password,
                'REQ_SN' => $cash_id,
            ),
            'QTRANSREQ' => array(
                'QUERY_SN' => $cash_id,
                'MERCHANT_ID' => $merchant_id,
                'STATUS' => '2',
                'TYPE' => '1',
                'START_DAY' => '',
                'END_DAY' => ''
            ),
        );

        // 发送请求
        $result = $tools->send($params);

        // 返回结果
        return $result;
    }


    /**
     * 如果返回成功状态码之后的操作
     */
    public function success()
    {
        // 有一些状态，可以不用重复测试，直接判断为最终成功，具体为：
        // 判断INFO状态码
        // 最终成功处理逻辑
        // 开启事务处理
        DB::beginTransaction();
        try {

            // 重新从数据库中读取这两条记录，用初始化的数据会被缓存，这可不是我们想要的
            // agentaccount表
            // 使用悲观锁，在读取的过程中不允许写入
            $agentaccount = DB::table('agent_accounts')->lockForUpdate()->where('agent_id', $this->agent->id)->first();

            // 当前提现数据库记录
            $withdraw = DB::table('withdraws')->lockForUpdate()->where('cash_id', $this->result['AIPG']['INFO']['REQ_SN'])->first();

            // 首先先把提现记录状态写入数据库，标记为成功
            $sql = "update withdraws set status = 1 where id = ".$withdraw->id;

            // 写入日志
            // 还原
            $msg = '提现记录状态写入数据库操作，待执行的sql语句为：'.$sql;
            Log::info($msg);

            if (!DB::update($sql)) {
                $msg = '提现记录标注成功操作失败了，请检查~'."\n".'执行sql失败，语句为：'.$sql."\n".'提现单号：'.$withdraw->cash_id;
                Log::info($msg);
                throw new \Exception($msg);
            }

            // 用户资产表逻辑，提现中金额减少，可利用金额保持不变
            // $cash_money = $agentaccount->cash_money - $withdraw->account;
            // 提现中金额 - $sum
            $cash_money = $agentaccount->cash_money - $withdraw->sum;
            // 总金额 - $sum
            $sum_money = $agentaccount->sum_money - $withdraw->sum;
            // 可用金额保持不变
            $sql = "update agent_accounts set cash_money = ".$cash_money.", sum_money = ".$sum_money." where id = ".$agentaccount->id;

            // 写入日志
            $msg = '用户资产表正在执行写入，待执行的sql语句为：'.$sql;
            Log::info($msg);

            // 执行
            if (!DB::update($sql)) {
                $msg = '用户资产表修正失败，请检查~'."\n".'执行sql失败，语句为：'.$sql."\n".'提现单号：'.$withdraw->cash_id;
                Log::info($msg);
                throw new \Exception($msg);
            }

            // 提交
            DB::commit();

            // 记录日志
            $msg = '';
            $msg .= $withdraw->remark."\n";
            $msg .= '当前用户openid：'.$this->openid.'，提现单号：'.$this->withdraw->cash_id.'交易成功，并且已成功写入数据库...'."\n";
            Log::info($msg);
            // 成功提示信息
            echo $msg;

            // 如果是微信提交的，那么需要把消息通过微信推送给用户
            // 提交成功模板
            // 需要获得关注用户的openid
            if ($this->openid) {
                $app = $this->getWechat();
                $app->template_message->send([
                    'touser' => $this->openid,
                    'template_id' => 'nXh_FRtLazHNeDBssYkvbSEGLY-5Nh8HND1FhaMwfXc',
                    'url' => '',
                    'data' => [
                        'first' => [
                            'value' => '您好，您已申请提现成功。',
                            'color' => '#173177',
                        ],
                        "keyword1" => [
                            "value" => $this->card->bank->name,
                            "color" => "#173177",
                        ],
                        "keyword2" => [
                            "value" => $this->card->card_number,
                            "color" => "#173177",
                        ],
                        "keyword3" => [
                            "value" => $this->agent->name, 
                            "color" => "#173177",
                        ],
                        "keyword4" => [
                            "value" => $this->withdraw->sum.'元', 
                            "color" => "#173177",
                        ],
                        "keyword5" => [
                            "value" => $this->withdraw->updated_at, 
                            "color" => "#173177",
                        ],
                        "remark" => [
                            "value"=>"本次提现预计2个工作日内到达您指定银行账户，请注意查询！", 
                            "color"=>"#173177",
                        ],
                    ],
                ]);
            }

        } catch (\Exception $e) {
            DB::rollback();
            echo $e->getMessage()."\n";
        }

        // 从队列中删除
        $this->delete();

    }

    /**
     * 如果返回中间状态码之后的操作
     */
    public function middle()
    {
        // 中间状态
        $msg = "\n".'提现单号：'.$this->withdraw->cash_id.' 第'.$this->attempts().'次状态更新失败了，将于'.$this->timeout.'秒后开始重试，请知悉...'."\n\n";
        Log::info($msg);
        echo $msg;

        // 过一段时间重新放入队列
        $this->release($this->timeout);
    }

    /**
     * 如果返回错误状态码之后的操作
     */
    public function fail()
    {
        // 判断提现状态
        // 但是分两种情况，一种是没到银行，支付公司接口验证没通过，是一种错误
        if ($this->result['AIPG']['INFO']['RET_CODE'] != '0000') {
            $current_err_code = $this->result['AIPG']['INFO']['RET_CODE'];
            $current_err_msg = $this->result['AIPG']['INFO']['ERR_MSG'];
        } else {
            $current_err_code = $this->result['AIPG']['QTRANSRSP']['QTDETAIL']['RET_CODE'];
            $current_err_msg = $this->result['AIPG']['QTRANSRSP']['QTDETAIL']['ERR_MSG'];
        }

        // 如果上面两个都通过了，说明已经是0000了，处理完毕
        $msg = "\n";
        $msg .= '目前提现单号：'.$this->withdraw->cash_id.'的提现状态码为：'.$current_err_code.'，提现失败，正在把结果写入数据库，请稍候...' . "\n";
        Log::info($msg);
        echo $msg;

        // 否则就为最终失败，而且是立即失败
        DB::beginTransaction();
        try {

            // 重新从数据库中读取这两条记录，用初始化的数据会被缓存，这可不是我们想要的
            // agentaccount表
            // 使用悲观锁，在读取的过程中不允许写入
            $agentaccount = DB::table('agent_accounts')->lockForUpdate()->where('agent_id', $this->agent->id)->first();

            // 当前提现数据库记录
            $withdraw = DB::table('withdraws')->lockForUpdate()->where('cash_id', $this->result['AIPG']['INFO']['REQ_SN'])->first();

            // 首先先把提现记录状态写入数据库，标记为失败
            $sql = "update withdraws set status = 2, err_code = ".$current_err_code.", err_msg = '".$current_err_msg."' where id = ".$withdraw->id;

            // 写入日志
            // 还原
            $msg = '正在执行将提现记录状态写入数据库操作，待执行的sql语句为：'.$sql;
            Log::info($msg);

            if (!DB::update($sql)) {
                $msg = '提现记录标注失败操作失败了，请检查~'."\n".'执行sql失败，语句为：'.$sql."\n".'提现单号：'.$withdraw->cash_id;
                Log::info($msg);
                throw new \Exception($msg);
            }

            // 用户资产表还原，提现中金额减少(-$account)，可利用金额增加(+$sum)，总金额(+$charge)
            // $cash_money = $agentaccount->cash_money - $withdraw->account;
            // $available_money = $agentaccount->available_money + $withdraw->sum;
            // $sum_money = $agentaccount->sum_money + $withdraw->charge;

            // 逻辑修改，用户资产表还原，提现中金额减少(-$sum)，可利用金额增加(+$sum)，总金额(保持不变)
            $cash_money = $agentaccount->cash_money - $withdraw->sum;
            $available_money = $agentaccount->available_money + $withdraw->sum;
            $sql = "update agent_accounts set cash_money = ".$cash_money.", available_money = ".$available_money." where id = ".$agentaccount->id;

            // 写入日志
            // 还原
            $msg = '用户资产表将要被还原，待执行的sql语句为：'.$sql;
            Log::info($msg);

            // 执行
            if (!DB::update($sql)) {
                $msg = '用户资产表还原失败，请检查~'."\n".'执行sql失败，语句为：'.$sql."\n".'提现单号：'.$withdraw->cash_id;
                Log::info($msg);
                throw new \Exception($msg);
            }

            // 提交
            DB::commit();

            // 最终返回提现失败
            $msg = '';
            $msg .= "\n";
            $msg .= '----------------Oh,My God,提现失败了----------------';
            $msg .= "\n";
            $msg .= '用户openid：'.$this->openid."\n";
            $msg .= '提现单号：'.$withdraw->cash_id."\n";
            $msg .= '错误代码：'.$current_err_code."\n";
            $msg .= '具体原因：'.$current_err_msg."\n";
            $msg .= '----------------失败结束标记----------------';
            $msg .= "\n";
            $msg .= "\n";

            // 写入日志
            Log::info($msg);
            echo $msg;

            // 如果是微信提交的，那么需要把消息通过微信推送给用户
            // 提交失败模板
            // 需要获得关注用户的openid
            if ($this->openid) {
                $app = $this->getWechat();
                $app->template_message->send([
                    'touser' => $this->openid,
                    'template_id' => 'itwKOvlLf5xRW2YnUkobBEPPWgFuUq6Ju-Way_Y84TA',
                    'url' => '',
                    'data' => [
                        'first' => [
                            'value' => '非常抱歉，提现申请失败！',
                            'color' => '#173177',
                        ],
                        "keyword1" => [
                            "value" => $this->withdraw->sum.'元', 
                            "color" => "#173177",
                        ],
                        "keyword2" => [
                            "value" => $this->withdraw->updated_at, 
                            "color" => "#173177",
                        ],
                        "keyword3" => [
                            "value" => $current_err_msg, 
                            "color" => "#173177",
                        ],
                        "remark" => [
                            "value"=>"请您按照失败原因修改相关信息后，重新提现！", 
                            "color"=>"#173177",
                        ],
                    ],
                ]);
            }

        } catch (\Exception $e) {
            DB::rollback();
            echo $e->getMessage()."\n";
        }
        // 从队列中删除
        $this->delete();
    }

    /**
     * 微信初始化
     */
    public function getWechat()
    {
        // 微信客户端
        // 全局配置
        $config = Config::get("wechat.official_account.default");
        
        // 使用配置来初始化一个公众号应用实例
        return Factory::officialAccount($config);
    }

    /**
     * 判断交易状态（到底是处理完了，还是没有处理完）
     */
    public function checkStatus()
    {
        // 判断交易状态，如果处理完了
        if ($this->result['AIPG']['INFO']['RET_CODE'] == '0000') {

            // 如果银行返回也是0000，那么说明转账成功
            if ($this->result['AIPG']['QTRANSRSP']['QTDETAIL']['RET_CODE'] == '0000') {
                // 转账成功
                $this->success();
            } else {
                // 转账失败
                $this->fail();
            }

        } elseif (
            // 如果是中间状态
            $this->result['AIPG']['INFO']['RET_CODE'] == '0003' || 
            $this->result['AIPG']['INFO']['RET_CODE'] == '0014' || 
            $this->result['AIPG']['INFO']['RET_CODE'] == '2000' || 
            $this->result['AIPG']['INFO']['RET_CODE'] == '2001' || 
            $this->result['AIPG']['INFO']['RET_CODE'] == '2003' || 
            $this->result['AIPG']['INFO']['RET_CODE'] == '2005' || 
            $this->result['AIPG']['INFO']['RET_CODE'] == '2007' || 
            $this->result['AIPG']['INFO']['RET_CODE'] == '2008'
        ) {
            // 那么就放入队列，隔一段时间再查
            $this->middle();
        } else {
            // 如果既不是0000，又不是中间状态，那么就彻底失败了
            $this->fail();
        }
    }

}
