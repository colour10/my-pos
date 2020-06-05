<?php

namespace App\Models;

/**
 * App\Models\Role
 *
 * @property int $id 主键ID
 * @property string $name 角色名称
 * @property string $description 角色描述
 * @property \Illuminate\Support\Carbon|null $created_at 操作时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Role extends Model
{
    // 角色拥有权限,多对多
    public function permissions()
    {
        return $this->belongsToMany('\App\Models\Permission', 'permission_role', 'role_id', 'permission_id')->withPivot(['role_id', 'permission_id']);
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
