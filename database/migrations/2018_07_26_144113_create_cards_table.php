<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键ID');
            $table->unsignedBigInteger('agent_id')->index()->comment('合伙人ID');
            $table->string('card_number', 19)->comment('银行卡号');
            $table->unsignedInteger('bank_id')->comment('开户行ID');
            $table->string('branch', 100)->comment('支行名称');
            $table->unsignedTinyInteger('isdefault')->index()->comment('是否为默认结算卡片，0：非默认，1：默认');
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
        Schema::dropIfExists('cards');
    }
}
