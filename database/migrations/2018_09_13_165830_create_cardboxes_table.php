<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardboxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cardboxes', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键ID');
            $table->string('merCardName')->comment('卡片名称');
            $table->text('merCardImg')->comment('办卡封面图');
            $table->text('advertiseImg')->default('')->comment('详情广告图');
            $table->string('merCardJinduImg')->nullable()->default('')->comment('进度封面图');
            $table->string('merCardOrderImg')->nullable()->default('')->comment('订单封面图');
            $table->unsignedBigInteger('sort')->nullable()->comment('排序');
            $table->decimal('cardBankAmount', 10, 2)->unsigned()->nullable()->comment('银行返佣金额');
            $table->decimal('cardAmount', 10, 2)->unsigned()->nullable()->comment('合伙人返佣金额');
            $table->decimal('cardTopAmount', 10, 2)->unsigned()->nullable()->comment('合伙人上级返佣金额');
            $table->text('cardContent')->nullable()->comment('卡片简介');
            $table->text('creditCardUrl')->nullable()->comment('办卡URL链接地址');
            $table->string('littleFlag')->nullable()->comment('办卡醒目标识，一般2-4个汉字之间');
            $table->text('creditCardJinduUrl')->nullable()->comment('查询卡片申请进度URL地址');
            $table->unsignedTinyInteger('status')->nullable()->default('1')->comment('卡片状态，0：禁用，1：启用');
            $table->string('rate')->default('0.00')->comment('下卡比率');
            $table->string('method')->default('')->comment('结算方式');
            $table->string('source')->default('')->comment('渠道来源');
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
        Schema::dropIfExists('cardboxes');
    }
}
