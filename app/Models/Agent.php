<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\Agent
 *
 * @property int $id 主键ID
 * @property string $sid 合伙人ID
 * @property string|null $sname 合伙人简称
 * @property string|null $name 姓名
 * @property string|null $id_number 身份证号
 * @property string|null $mobile 联系电话
 * @property string|null $password 登录密码
 * @property string|null $cash_password 提现密码
 * @property string|null $remember_token 用户登录唯一标识符
 * @property string|null $wx_openid 微信用户的最新openid值
 * @property string|null $openid 微信用户的原始openid值
 * @property string|null $parentopenid 上级用户的原始openid值
 * @property int $status 审核状态，0：未审核，1：审核通过，2：审核未通过
 * @property int $method 注册方式，1：管理员后台开户，2：办卡自动添加，3：微信主动注册，4：实名认证注册，5：首页授权添加
 * @property \Illuminate\Support\Carbon|null $created_at 操作时间
 * @property \Illuminate\Support\Carbon|null $updated_at 更新时间
 * @property-read \App\Models\AgentAccount $agentaccount
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ApplyCard[] $applycards
 * @property-read \App\Models\Card $card
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Card[] $cards
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Finance[] $finances
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Finance[] $passedfinances
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Withdraw[] $withdraws
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereCashPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereOpenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereParentopenid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereSname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Agent whereWxOpenid($value)
 * @mixin \Eloquent
 */
class Agent extends Authenticatable
{
    use Notifiable;
    protected $guarded = [];

    // 合伙人-调账记录，一对多
    public function finances()
    {
        return $this->hasMany(Finance::class, 'agent_id', 'id');
    }

    // 合伙人-已经审核通过的调账记录，一对多
    public function passedfinances()
    {
        return $this->hasMany(Finance::class, 'agent_id', 'id')->where('status', '1');
    }

    // 合伙人-合伙人账户资金，一对一
    public function agentaccount()
    {
        return $this->hasOne(AgentAccount::class, 'agent_id', 'id');
    }

    // 合伙人-银行卡，一对多
    public function cards()
    {
        return $this->hasMany(Card::class, 'agent_id', 'id');
    }

    // 合伙人-银行卡，因为只有一张银行卡，所以是一对一
    public function card()
    {
        return $this->hasOne(Card::class, 'agent_id', 'id')->where('isdefault', 1);
    }

    // 合伙人-提现记录，一对多
    public function withdraws()
    {
        return $this->hasMany(Withdraw::class, 'agent_id', 'id');
    }

    // 合伙人-申请卡片记录，一对多
    public function applycards()
    {
        return $this->hasMany(ApplyCard::class, 'agent_id', 'id');
    }

}
