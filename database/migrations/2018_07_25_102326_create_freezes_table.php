<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFreezesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freezes', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键ID');
            $table->unsignedBigInteger('agent_id')->index()->comment('合伙人ID');
            $table->unsignedBigInteger('operater')->comment('操作人ID');
            $table->unsignedInteger('account_type')->index()->comment('账户类型');
            $table->decimal('amount', 10, 2)->unsigned()->comment('调账金额');
            $table->string('description', 200)->comment('调账原因');
            $table->unsignedTinyInteger('status')->default(1)->index()->comment('开启状态，0：禁用，1：启用');
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
        Schema::dropIfExists('freezes');
    }
}
