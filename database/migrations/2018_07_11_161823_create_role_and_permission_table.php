<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleAndPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("roles", function(Blueprint $table){
           $table->increments('id')->comment('主键ID');
           $table->string('name')->comment('角色名称');
           $table->string('description')->comment('角色描述');
           $table->timestamp('created_at')->nullable()->comment('操作时间');
           $table->timestamp('updated_at')->nullable()->comment('更新时间');
        });

        Schema::create("permissions", function(Blueprint $table){
            $table->increments('id')->comment('主键ID');
            $table->unsignedInteger('pid')->comment('所属权限ID');
            $table->string('name')->comment('权限名称');
            $table->string('description')->comment('权限描述');
            $table->timestamp('created_at')->nullable()->comment('操作时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
        });

        Schema::create("permission_role", function(Blueprint $table){
            $table->increments('id')->comment('主键ID');
            $table->integer("role_id")->comment('角色ID');
            $table->integer("permission_id")->comment('权限ID');
            $table->timestamp('created_at')->nullable()->comment('操作时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
        });

        Schema::create("role_user", function(Blueprint $table){
            $table->increments('id')->comment('主键ID');
            $table->integer("role_id")->comment('角色ID');
            $table->integer("user_id")->comment('用户ID');
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
        Schema::drop('roles');
        Schema::drop('permissions');
        Schema::drop('permission_role');
        Schema::drop('role_user');
    }
}
