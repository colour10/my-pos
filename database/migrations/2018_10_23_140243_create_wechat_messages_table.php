<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWechatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wechat_messages', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键ID');
            $table->string('ask_openid')->index()->comment('提问者微信openid');
            $table->text('ask_user')->nullable()->comment('提问者微信用户信息');
            $table->text('ask_msg')->nullable()->comment('提问内容');
            $table->string('answer_openid')->nullable()->comment('回复者微信openid');
            $table->text('answer_msg')->nullable()->comment('回复内容');
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
        Schema::dropIfExists('wechat_messages');
    }
}
