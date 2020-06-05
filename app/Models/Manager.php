<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\Manager
 *
 * @property int $id 主键ID
 * @property string $name 姓名
 * @property string $mobile 手机号
 * @property string $password 登录密码
 * @property string|null $remember_token
 * @property string $email 邮箱
 * @property int $creater 创建者ID
 * @property string|null $last_login_at 最后登录时间
 * @property int $status 管理员状态，0：停用，1：启用
 * @property \Illuminate\Support\Carbon|null $created_at 操作时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Manager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Manager newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Manager query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Manager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Manager whereCreater($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Manager whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Manager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Manager whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Manager whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Manager whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Manager wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Manager whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Manager whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Manager whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Manager extends Authenticatable
{
    use Notifiable;
    protected $guarded = [];

    // 管理员-角色，多对多
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id')->withPivot(['user_id', 'role_id']);
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
        return !!$roles->intersect($this->roles)->count();
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
