<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_accounts', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键ID');
            $table->unsignedBiginteger('agent_id')->index()->comment('合伙人ID');
            $table->decimal('frozen_money', 10, 2)->unsigned()->comment('冻结资金');
            $table->decimal('available_money', 10, 2)->unsigned()->comment('可用资金');
            $table->decimal('cash_money', 10, 2)->default(0.00)->unsigned()->comment('提现中资金');
            $table->decimal('sum_money', 10, 2)->unsigned()->comment('账户总余额');
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
        Schema::dropIfExists('agent_accounts');
    }
}
