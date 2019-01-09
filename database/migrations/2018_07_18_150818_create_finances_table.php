<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finances', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键ID');
            $table->unsignedBigInteger('agent_id')->index()->comment('商户ID');
            $table->unsignedBigInteger('creater')->nullable()->comment('创建人ID');
            $table->unsignedBigInteger('excel_id')->nullable()->default('0')->index()->comment('从Excel文件中导入的序号ID');
            $table->unsignedInteger('account_type')->index()->comment('账户类型');
            $table->unsignedTinyInteger('type')->index()->comment('调账类型，1：调入，2：调出');
            $table->decimal('amount', 10, 2)->unsigned()->comment('调账金额');
            $table->string('description', 200)->comment('调账原因');
            $table->unsignedTinyInteger('status')->index()->comment('财务审核状态,0：未审核,1：审核通过,2:审核失败');
            $table->unsignedBigInteger('operater')->nullable()->comment('审核人ID');
            $table->timestamp('operated_at')->nullable()->comment('审核时间');
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
        Schema::dropIfExists('finances');
    }
}
