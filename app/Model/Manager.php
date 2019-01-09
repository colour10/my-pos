<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Manager extends Authenticatable
{
    use Notifiable;
    protected $guarded = [];

    // 管理员-角色，多对多
    public function roles() 
    {
        return $this->belongsToMany(\App\Model\Role::class, 'role_user', 'user_id', 'role_id')->withPivot(['user_id', 'role_id']);
    }

    // 管理员分配角色
    public function assignRole($role) 
    {
        return $this->roles()->save($role);
    }

    // 管理员删除角色
    public function deleteRole($role) 
    {
        return $this->roles()->detach($role);
    }

    /*
     * 是否有某个角色
     */
    public function isInRoles($roles)
    {
        return !! $roles->intersect($this->roles)->count();
    }

    /*
     * 管理员是否有权限
     */
    public function hasPermission($permission)
    {
        // 如果不存在，就使用403页面
        if (!$permission) {
            return view('errors.403');
        }
        return $this->isInRoles($permission->roles);
    }

}
