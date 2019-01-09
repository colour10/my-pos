<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApplyCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apply_cards', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键ID');
            $table->string('order_id')->index()->comment('申请订单号');
            $table->string('user_openid')->index()->comment('申请人微信openid');
            $table->unsignedBiginteger('card_id')->comment('申请卡片ID');
            $table->string('invite_openid')->nullable()->comment('邀请人微信openid');
            $table->string('top_openid')->nullable()->comment('邀请人上级微信openid');
            $table->decimal('invite_money', 10, 2)->default('0.00')->comment('邀请人返现佣金');
            $table->decimal('top_money', 10, 2)->default('0.00')->comment('邀请人上级返现佣金');
            $table->string('user_name')->nullable()->comment('申请人姓名');
            $table->string('user_identity')->nullable()->comment('申请人身份证号');
            $table->string('user_phone')->nullable()->comment('申请人手机号');
            $table->unsignedTinyInteger('status')->default('0')->comment('卡片申请状态，0：审核中; 1：审核通过；2：审核不通过；3：无记录');
            $table->timestamp('created_at')->nullable()->comment('申请时间');
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
        Schema::dropIfExists('apply_cards');
    }
}
