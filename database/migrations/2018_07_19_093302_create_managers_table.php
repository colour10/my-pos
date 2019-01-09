<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('managers', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键ID');
            $table->string('name', 20)->index()->comment('姓名');
            $table->char('mobile', 11)->index()->comment('手机号');
            $table->string('password')->comment('登录密码');
            $table->rememberToken();
            $table->string('email', 50)->comment('邮箱');
            $table->unsignedInteger('creater')->comment('创建者ID');
            $table->dateTime('last_login_at')->nullable()->comment('最后登录时间');
            $table->unsignedTinyInteger('status')->index()->comment('管理员状态，0：停用，1：启用');
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
        Schema::dropIfExists('managers');
    }
}
