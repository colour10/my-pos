<?php

namespace App\Model;

use App\Model;

class Role extends Model
{
    // 角色拥有权限,多对多
    public function permissions() 
    {
        return $this->belongsToMany('\App\Model\Permission', 'permission_role', 'role_id', 'permission_id')->withPivot(['role_id', 'permission_id']);
    }

    // 角色授权权限
    public function grantPermission($permission) 
    {
        return $this->permissions()->save($permission);
    }

    // 删除角色的权限
    public function deletePermission($permission) 
    {
        return $this->permissions()->detach($permission);
    }

    // 角色是否有权限
    public function hasPermission($permission) 
    {
        return $this->permissions->contains($permission);
    }

}
