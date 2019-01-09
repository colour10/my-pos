<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechats', function (Blueprint $table) {
            $table->increments('id')->comment('主键ID');
            $table->string('aid')->nullable()->comment('公众号对应id');

            //微信公众号设置参数
            $table->string('wechat_app_id')->nullable()->comment('AppID');
            $table->string('wechat_secret')->nullable()->comment('AppSecret');
            $table->string('wechat_token')->nullable()->comment('Token');
            $table->string('wechat_aes_key')->nullable()->comment('EncodingAESKey，兼容与安全模式下请一定要填写！！！');

            //微信支付设置参数
            $table->string('pay_mch_id')->nullable()->comment('');  
            $table->string('pay_api_key')->nullable()->comment('');
            $table->string('pay_cert_path')->nullable()->comment('');
            $table->string('pay_key_path')->nullable()->comment('');

            //微信开放平台设置参数
            $table->string('op_app_id')->nullable()->comment(''); 
            $table->string('op_secret')->nullable()->comment('');
            $table->string('op_token')->nullable()->comment('');
            $table->string('op_aes_key')->nullable()->comment('');

            //微信企业号设置参数
            $table->string('work_corp_id')->nullable()->comment('');
            $table->string('work_agent_id')->nullable()->comment('');
            $table->string('work_secret')->nullable()->comment('');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wechats');
    }
}
