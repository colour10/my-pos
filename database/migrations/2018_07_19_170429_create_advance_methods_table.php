<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvanceMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advance_methods', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键ID');
            $table->string('name')->comment('支付通道名称');
            $table->string('gateway')->nullable()->comment('支付通道网关');
            $table->string('acctno')->nullable()->comment('支付通道账户号');
            $table->string('username')->comment('支付通道登录用户名');
            $table->string('password')->comment('支付通道登录密码');
            $table->string('merchant_id')->comment('支付通道商户代码');
            $table->string('bank_code')->nullable()->comment('所属银行代码');
            $table->string('business_code')->comment('业务类型');
            $table->decimal('max', 10, 2)->unsigned()->comment('单笔最高金额');
            $table->decimal('cost_rate', 10, 2)->unsigned()->comment('成本费率');
            $table->decimal('contract_rate', 10, 2)->unsigned()->comment('签约费率');
            $table->decimal('per_charge', 10, 2)->unsigned()->comment('单笔结算费用');
            $table->unsignedTinyInteger('status')->default(0)->index()->comment('开启状态，0：禁用，1：启用');
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
        Schema::dropIfExists('advance_methods');
    }
}
