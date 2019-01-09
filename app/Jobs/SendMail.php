<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    // 属性
    protected $message;
    // 发送模版
    protected $template;
    // 重试时间间隔
    protected $timeout;
    // 最大重试次数，最多10次
    protected $attempt;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message, $template="emails.default", $timeout=120, $attempt=10)
    {
        // 邮件内容
        $this->message = $message;
        // 发送模版
        $this->template = $template;
        // 重试时间间隔
        $this->timeout = $timeout;
        // 重试次数
        $this->attempt = $attempt;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 判断逻辑
        if ($this->attempts() > $this->attempt) {

            // 如果超过最大次数，那么就确认为失败，并且删除队列中的任务
            $this->fail();
            
        } else {

            // 第n次邮件发送
            $msg = PHP_EOL.'正在进行第'.$this->attempts().'次邮件发送，请稍候...'.PHP_EOL.PHP_EOL;
            Log::info($msg);
            echo $msg;

            // 重新发送邮件，并认证
            $this->checkStatus();

        }
    }

    /**
     * 发送邮件逻辑
     */
    public function send()
    {
        // 逻辑
        Mail::send($this->template, ['content' => $this->message['content']], function($message) {
            $message ->to($this->message['to'])->subject($this->message['title']);
            // 如果有附件
            if (!empty($this->message['attachment'])) {
                // 取出附件地址的文件名和扩展名
                $pathinfo = pathinfo($this->message['attachment']);
                //在邮件中上传附件
                $message->attach($this->message['attachment'], ['as'=>"=?UTF-8?B?".base64_encode($this->message['attachment_filename'])."?=.".$pathinfo['extension']]);
            }
        });
        // 返回成功与否
        return count(Mail::failures());
    }

    /**
     * 检查邮件发送状态
     */
    public function checkStatus()
    {
        $result = $this->send();
        if ($result == 0) {
            // 发送成功
            $this->success();
        } else {
            // 发送失败，不过暂定为中间状态
            $this->middle();
        }
    }

    /**
     * 邮件发送成功
     */
    public function success()
    {
        // 成功提示
        $msg = '';
        $msg .= '<pre>'.PHP_EOL;
        $msg .= '邮件发送成功~'.PHP_EOL;
        $msg .= '邮件内容如下：'.PHP_EOL;
        $arr = print_r($this->message, true);
        $msg .= "$arr";
        $msg .= "\n\n";
        // 将报文内容写入日志
        Log::info($msg);
        echo $msg;
        // 从队列中删除
        $this->delete();
    }

    /**
     * 邮件发送失败-需要重试，中间状态
     */
    public function middle()
    {
        // 中间状态
        $msg = PHP_EOL.'邮件第'.$this->attempts().'次发送失败，将于'.$this->timeout.'秒后开始重试，请知悉....'.PHP_EOL.PHP_EOL;
        Log::info($msg);
        echo $msg;

        // 过一段时间重新放入队列
        $this->release($this->timeout);
    }

    /**
     * 邮件发送失败
     */
    public function fail()
    {
        // 中间状态
        $msg = PHP_EOL.'邮件第'.$this->attempts().'次发送失败，邮件彻底发送失败，请检查，当前任务将要被删除，请知悉...'.PHP_EOL.PHP_EOL;
        Log::info($msg);
        echo $msg;

        // 彻底删除
        $this->delete();
    }

}
