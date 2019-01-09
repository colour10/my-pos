<?php

namespace App\Model;

use App\Model;

class Permission extends Model
{
    // 权限-角色，权限属于哪些角色，多对多
    public function roles() {
        return $this->belongsToMany('\App\Model\Role', 'permission_role', 'permission_id', 'role_id')->withPivot(['permission_id', 'role_id']);
    }
}
