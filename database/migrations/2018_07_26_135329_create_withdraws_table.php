<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraws', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键ID');
            $table->string('cash_id')->index()->comment('结算订单号');
            $table->unsignedBigInteger('agent_id')->index()->comment('合伙人ID');
            $table->unsignedBigInteger('method_id')->index()->comment('结算通道ID');
            $table->decimal('sum', 10, 2)->unsigned()->comment('结算金额');
            $table->decimal('charge', 10, 2)->unsigned()->comment('手续费');
            $table->decimal('account', 10, 2)->unsigned()->comment('实际到账金额');
            $table->unsignedBigInteger('card_id')->comment('结算银行卡ID');
            $table->string('remark')->nullable()->comment('转账附言');
            $table->unsignedTinyInteger('status')->default(0)->index()->comment('结算状态，0：结算中，1：成功，2：失败');
            $table->unsignedBigInteger('err_code')->nullable()->comment('结算失败代码');
            $table->string('err_msg')->nullable()->comment('结算失败原因');
            $table->timestamp('created_at')->nullable()->comment('结算时间');
            $table->timestamp('updated_at')->nullable()->comment('成功时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdraws');
    }
}
