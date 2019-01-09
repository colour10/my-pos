<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键ID');
            $table->char('sid', 6)->index()->comment('合伙人ID');
            $table->string('sname', 20)->nullable()->comment('合伙人简称');
            $table->string('name', 20)->nullable()->comment('姓名');
            $table->char('id_number', 18)->nullable()->comment('身份证号');
            $table->char('mobile', 11)->nullable()->comment('联系电话');
            $table->string('password')->nullable()->comment('登录密码');
            $table->string('cash_password')->nullable()->comment('提现密码');
            $table->rememberToken()->nullable()->comment('用户登录唯一标识符');
            $table->string('wx_openid')->index()->nullable()->comment('微信用户的最新openid值');
            $table->string('openid')->index()->nullable()->comment('微信用户的原始openid值');
            $table->string('parentopenid')->index()->nullable()->comment('上级用户的原始openid值');
            $table->unsignedTinyInteger('status')->index()->default('1')->comment('审核状态，0：未审核，1：审核通过，2：审核未通过');
            $table->unsignedTinyInteger('method')->index()->default('1')->comment('注册方式，1：管理员后台开户，2：办卡自动添加，3：微信主动注册，4：实名认证注册，5：首页授权添加');
            $table->timestamp('created_at')->nullable()->comment('操作时间');
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
        Schema::dropIfExists('agents');
    }
}
